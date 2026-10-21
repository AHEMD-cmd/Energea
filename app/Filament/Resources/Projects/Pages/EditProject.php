<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $upload = $data['image_upload'] ?? null;

        unset($data['image_upload']);

        if (is_string($upload) && $upload !== '') {
            $current = $this->getRecord()->image;

            $data['image'] = $upload;

            if (is_string($current) && str_starts_with($current, 'projects/') && $current !== $upload) {
                Storage::disk('public')->delete($current);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->fillForm();
    }
}
