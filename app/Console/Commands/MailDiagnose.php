<?php

namespace App\Console\Commands;

use App\Models\SiteSetting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('energea:mail-diagnose {email? : Send a test message to this address}')]
#[Description('Show the configured mail transport, test connectivity and optionally send a test message')]
class MailDiagnose extends Command
{
    /**
     * @var array<int, array{0: string, 1: int}>
     */
    private array $targets = [
        ['smtp.gmail.com', 587],
        ['smtp.gmail.com', 465],
        ['smtp-relay.gmail.com', 587],
        ['api.resend.com', 443],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $mailer = (string) config('mail.default');
        $config = (array) config("mail.mailers.{$mailer}", []);

        $this->table(['Setting', 'Value'], [
            ['MAIL_MAILER', $mailer],
            ['MAIL_HOST', $config['host'] ?? '—'],
            ['MAIL_PORT', $config['port'] ?? '—'],
            ['MAIL_SCHEME', $config['scheme'] ?? '—'],
            ['MAIL_USERNAME', $config['username'] ?? '—'],
            ['MAIL_FROM_ADDRESS', config('mail.from.address') ?? '—'],
            ['MAIL_TIMEOUT', $config['timeout'] ?? '—'],
        ]);

        if (($config['host'] ?? null) && ($config['port'] ?? null)) {
            $this->check((string) $config['host'], (int) $config['port'], 'configured transport');
        }

        foreach ($this->targets as [$host, $port]) {
            $this->check($host, $port, 'alternative');
        }

        $email = $this->argument('email');

        if (! is_string($email) || $email === '') {
            $email = $this->defaultRecipient();
        }

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->warn('No valid recipient supplied, skipping the test message.');

            return self::SUCCESS;
        }

        $this->line("Sending a test message to {$email}…");

        try {
            Mail::raw('This is a test message from the Energea website.', function ($message) use ($email): void {
                $message->to($email)->subject('Energea mail test');
            });
        } catch (\Throwable $exception) {
            $this->error(get_class($exception).': '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info('The mail transport accepted the test message.');

        return self::SUCCESS;
    }

    private function defaultRecipient(): ?string
    {
        try {
            $recipient = data_get(SiteSetting::current(), 'quote_recipients.0');
        } catch (\Throwable) {
            $recipient = null;
        }

        return is_string($recipient) ? $recipient : config('mail.from.address');
    }

    private function check(string $host, int $port, string $label): void
    {
        $timeout = (int) config('mail.mailers.smtp.timeout', 10);
        $start = microtime(true);
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if ($socket === false) {
            $this->line(sprintf('  %-20s %s:%d — BLOCKED (%s %s, %.2fs)', $label, $host, $port, $errno, $errstr, microtime(true) - $start));

            return;
        }

        fclose($socket);
        $this->line(sprintf('  %-20s %s:%d — open (%.2fs)', $label, $host, $port, microtime(true) - $start));
    }
}
