<?php

namespace App\Console\Commands;

use App\Enums\PartyType;
use App\Models\Certificate;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectParty;
use App\Models\Sector;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceGroupItem;
use App\Models\SiteSetting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

#[Signature('energea:import-preview {--path= : Frontend preview directory (defaults to config energea.frontend_path)}')]
#[Description('Import the static preview catalog (data.js + images) into the database')]
class ImportPreviewData extends Command
{
    private const EXPECTED_COUNTS = [
        'services' => 9,
        'projects' => 10,
        'sectors' => 8,
        'clients' => 38,
        'certifications' => 18,
    ];

    private const EXPECTED_PARTY_COUNT = 14;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $frontendPath = $this->option('path') ?: config('energea.frontend_path');

        if (! is_string($frontendPath) || ! is_dir($frontendPath)) {
            $this->error('Frontend preview directory not found: '.var_export($frontendPath, true));

            return self::FAILURE;
        }

        try {
            $data = $this->loadData($frontendPath.'/assets/js/data.js');
            $this->validateData($data);
            $this->copyImages($data, $frontendPath);
            $imported = DB::transaction(fn (): array => $this->import($data, $frontendPath));
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->table(['Entity', 'Imported', 'In database'], [
            ['Services', $imported['services'], Service::count()],
            ['Service groups', $imported['groups'], ServiceGroup::count()],
            ['Service group items', $imported['items'], ServiceGroupItem::count()],
            ['Sectors', $imported['sectors'], Sector::count()],
            ['Projects', $imported['projects'], Project::count()],
            ['Project parties', $imported['parties'], ProjectParty::count()],
            ['Clients', $imported['clients'], Client::count()],
            ['Certificates', $imported['certificates'], Certificate::count()],
        ]);

        $this->info('Preview catalog imported successfully.');

        return self::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadData(string $file): array
    {
        if (! is_file($file)) {
            throw new RuntimeException('data.js not found at '.$file);
        }

        $raw = file_get_contents($file);
        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');

        if ($start === false || $end === false || $end <= $start) {
            throw new RuntimeException('Could not locate the JSON object inside data.js.');
        }

        $payload = substr($raw, $start, $end - $start + 1);
        $payload = preg_replace('/^[ \t]*\/\/.*$/m', '', $payload);

        $data = json_decode($payload, true);

        if (! is_array($data)) {
            throw new RuntimeException('data.js is not valid JSON: '.json_last_error_msg());
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validateData(array $data): void
    {
        foreach (self::EXPECTED_COUNTS as $key => $expected) {
            $actual = is_array($data[$key] ?? null) ? count($data[$key]) : 0;

            if ($actual !== $expected) {
                throw new RuntimeException("Expected {$expected} entries in {$key}, found {$actual}.");
            }
        }

        $serviceSlugs = array_column($data['services'], 'slug');
        $detailSlugs = array_keys($data['serviceDetails']);

        if (array_diff($serviceSlugs, $detailSlugs) !== [] || array_diff($detailSlugs, $serviceSlugs) !== []) {
            throw new RuntimeException('Service list and service detail slugs do not match.');
        }

        $parties = $this->countParties($data['projects']);

        if ($parties !== self::EXPECTED_PARTY_COUNT) {
            throw new RuntimeException('Expected '.self::EXPECTED_PARTY_COUNT." importable parties, found {$parties}.");
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $projects
     */
    private function countParties(array $projects): int
    {
        $count = 0;

        foreach ($projects as $project) {
            foreach ($this->partyKeys() as $key) {
                foreach ($project[$key] ?? [] as $party) {
                    if (isset($party['name'], $party['src'])) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function copyImages(array $data, string $frontendPath): void
    {
        foreach ($data['services'] as $service) {
            $this->copyImage($frontendPath, $service['image'], $this->relativeImagePath('services/'.basename($service['image'])));
        }

        foreach ($data['projects'] as $project) {
            $this->copyImage($frontendPath, $project['image'], $this->relativeImagePath('projects/'.basename($project['image'])));

            foreach ($this->partyKeys() as $key) {
                foreach ($project[$key] ?? [] as $party) {
                    if (isset($party['src'])) {
                        $this->copyImage($frontendPath, $party['src'], 'projects/parties/'.basename($party['src']));
                    }
                }
            }
        }

        foreach ($data['clients'] as [, $code]) {
            $source = $this->findClientLogo($frontendPath, $code);
            $this->copyImage($frontendPath, $source, 'clients/'.basename($source));
        }

        foreach ($data['certifications'] as $certificate) {
            $this->copyImage($frontendPath, $this->certificateRelativePath($certificate), 'certificates/'.$this->certificateFileName($certificate));
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, int>
     */
    private function import(array $data, string $frontendPath): array
    {
        $counts = ['groups' => 0, 'items' => 0, 'parties' => 0];

        foreach ($data['sectors'] as $index => $sector) {
            Sector::updateOrCreate(
                ['code' => $sector['code']],
                [
                    'title' => $sector['title'],
                    'text' => $sector['text'],
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );
        }

        foreach ($data['services'] as $index => $service) {
            $detail = $data['serviceDetails'][$service['slug']];

            $record = Service::updateOrCreate(
                ['slug' => $service['slug']],
                [
                    'number' => $service['n'],
                    'image' => $this->relativeImagePath('services/'.basename($service['image'])),
                    'title' => $service['title'],
                    'type' => $service['type'],
                    'summary' => $service['summary'],
                    'overview' => $detail['overview'],
                    'steps' => $detail['steps'] ?? null,
                    'facts' => $detail['facts'] ?? null,
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );

            $groupCounts = $this->syncGroups($record, $detail['groups']);
            $counts['groups'] += $groupCounts['groups'];
            $counts['items'] += $groupCounts['items'];
        }

        foreach ($data['projects'] as $index => $project) {
            $record = Project::updateOrCreate(
                ['slug' => $project['slug']],
                [
                    'image' => $this->relativeImagePath('projects/'.basename($project['image'])),
                    'year' => (string) $project['year'],
                    'title' => $project['title'],
                    'location' => $project['location'],
                    'scope' => $project['scope'],
                    'systems' => $project['systems'] ?? null,
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );

            $counts['parties'] += $this->syncParties($record, $project);
        }

        foreach ($data['clients'] as $index => [$name, $code]) {
            $source = $this->findClientLogo($frontendPath, $code);

            Client::updateOrCreate(
                ['logo_code' => $code],
                [
                    'name' => ['en' => $name],
                    'logo' => 'clients/'.basename($source),
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );
        }

        foreach ($data['certifications'] as $index => $certificate) {
            Certificate::updateOrCreate(
                ['image' => 'certificates/'.$this->certificateFileName($certificate)],
                [
                    'name' => ['en' => $certificate['name']],
                    'type' => $certificate['type'],
                    'is_published' => true,
                    'sort_order' => $index,
                ],
            );
        }

        SiteSetting::firstOrCreate(['id' => 1], $this->siteSettingValues());

        return [
            'services' => count($data['services']),
            'sectors' => count($data['sectors']),
            'projects' => count($data['projects']),
            'clients' => count($data['clients']),
            'certificates' => count($data['certifications']),
            'groups' => $counts['groups'],
            'items' => $counts['items'],
            'parties' => $counts['parties'],
        ];
    }

    /**
     * @param  array<string, mixed>  $groups
     * @return array{groups: int, items: int}
     */
    private function syncGroups(Service $service, array $groups): array
    {
        $english = $groups['en'] ?? [];
        $arabic = $groups['ar'] ?? [];

        if (count($english) !== count($arabic)) {
            throw new RuntimeException("Group counts differ between locales for service {$service->slug}.");
        }

        $keptGroupIds = [];
        $itemCount = 0;

        foreach ($english as $index => $group) {
            $itemsEn = $group['items'] ?? [];
            $itemsAr = $arabic[$index]['items'] ?? [];

            if (count($itemsEn) !== count($itemsAr)) {
                throw new RuntimeException("Group {$index} item counts differ between locales for service {$service->slug}.");
            }

            $record = ServiceGroup::updateOrCreate(
                ['service_id' => $service->id, 'sort_order' => $index],
                ['title' => ['en' => $group['title'], 'ar' => $arabic[$index]['title']]],
            );

            $keptItemIds = [];

            foreach ($itemsEn as $itemIndex => $text) {
                $item = ServiceGroupItem::updateOrCreate(
                    ['service_group_id' => $record->id, 'sort_order' => $itemIndex],
                    ['text' => ['en' => $text, 'ar' => $itemsAr[$itemIndex]]],
                );

                $keptItemIds[] = $item->id;
                $itemCount++;
            }

            ServiceGroupItem::where('service_group_id', $record->id)
                ->whereNotIn('id', $keptItemIds)
                ->delete();

            $keptGroupIds[] = $record->id;
        }

        ServiceGroup::where('service_id', $service->id)
            ->whereNotIn('id', $keptGroupIds)
            ->delete();

        return ['groups' => count($english), 'items' => $itemCount];
    }

    /**
     * @param  array<string, mixed>  $project
     */
    private function syncParties(Project $record, array $project): int
    {
        $kept = [];
        $sort = 0;

        foreach ([[PartyType::Client, 'clients'], [PartyType::Contractor, 'contractors'], [PartyType::Consultant, 'consultants']] as [$type, $key]) {
            foreach ($project[$key] ?? [] as $party) {
                if (! isset($party['name'], $party['src'])) {
                    continue;
                }

                $item = ProjectParty::updateOrCreate(
                    ['project_id' => $record->id, 'party_type' => $type, 'sort_order' => $sort],
                    [
                        'name' => $party['name'],
                        'logo' => 'projects/parties/'.basename($party['src']),
                    ],
                );

                $kept[] = $item->id;
                $sort++;
            }
        }

        $record->parties()->whereNotIn('id', $kept)->delete();

        return count($kept);
    }

    /**
     * @return array<string, mixed>
     */
    private function siteSettingValues(): array
    {
        return [
            'company_name' => 'Energea',
            'footer_text' => [
                'en' => 'Energy Solutions Technologies. Hands-on electrical testing and commissioning, alongside comprehensive O&M services for all building systems.',
                'ar' => 'تقنيات حلول الطاقة. اختبارات كهربائية ميدانية وتشغيل تجريبي، إلى جانب خدمات تشغيل وصيانة شاملة لجميع أنظمة المباني.',
            ],
            'general_email' => 'hello@energea.me',
            'quote_recipients' => ['hello@energea.me'],
            'departments' => [
                [
                    'name' => ['en' => 'Electrical Testing', 'ar' => 'الاختبارات الكهربائية'],
                    'phone' => '+966 54 452 0490',
                    'email' => 'Nader@energea.me',
                ],
                [
                    'name' => ['en' => 'Operations & Maintenance', 'ar' => 'التشغيل والصيانة'],
                    'phone' => '+966 55 629 4274',
                    'email' => 'mubarak.rashid@energea.me',
                ],
            ],
            'offices' => [
                [
                    'name' => ['en' => 'Riyadh office', 'ar' => 'مكتب الرياض'],
                    'map_url' => 'https://www.google.com/maps/search/?api=1&query=Riyadh%2C+Saudi+Arabia',
                ],
                [
                    'name' => ['en' => 'Jeddah office', 'ar' => 'مكتب جدة'],
                    'map_url' => 'https://www.google.com/maps/search/?api=1&query=Jeddah%2C+Saudi+Arabia',
                ],
                [
                    'name' => ['en' => 'Al Khobar office', 'ar' => 'مكتب الخبر'],
                    'map_url' => 'https://www.google.com/maps/search/?api=1&query=Al+Khobar%2C+Saudi+Arabia',
                ],
            ],
            'social_links' => [],
            'og_image' => 'images/social-icon-preview.jpg',
        ];
    }

    private function relativeImagePath(string $relative): string
    {
        return str_replace('\\', '/', $relative);
    }

    private function copyImage(string $frontendPath, string $relativeSource, string $relativeDestination): void
    {
        $source = $frontendPath.'/'.str_replace('\\', '/', $relativeSource);

        if (! is_file($source)) {
            throw new RuntimeException('Missing image: '.$relativeSource);
        }

        Storage::disk('public')->put($relativeDestination, file_get_contents($source));
    }

    private function findClientLogo(string $frontendPath, string $code): string
    {
        $matches = glob($frontendPath.'/assets/images/clients/logo-'.$code.'.*');

        if ($matches === false || $matches === []) {
            throw new RuntimeException("Missing client logo for code {$code}.");
        }

        return 'assets/images/clients/'.basename($matches[0]);
    }

    /**
     * @param  array<string, mixed>  $certificate
     */
    private function certificateRelativePath(array $certificate): string
    {
        return 'assets/images/certificates/'.$this->certificateFileName($certificate);
    }

    /**
     * @param  array<string, mixed>  $certificate
     */
    private function certificateFileName(array $certificate): string
    {
        return 'certificate-'.$certificate['page'].'.'.($certificate['format'] ?? 'jpg');
    }

    /**
     * @return array<int, string>
     */
    private function partyKeys(): array
    {
        return ['clients', 'contractors', 'consultants'];
    }
}
