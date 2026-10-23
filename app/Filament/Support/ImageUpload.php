<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

class ImageUpload
{
    /**
     * An image field whose current preview doubles as the replace button.
     */
    public static function make(string $name, string $directory, bool $required = true, ?string $label = null): FileUpload
    {
        return FileUpload::make($name)
            ->label($label ?? __('admin.fields.image'))
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->maxSize(5120)
            ->helperText(__('admin.descriptions.image_replace'))
            ->extraAttributes([
                'style' => 'cursor: pointer',
                'x-on:click' => "! \$event.target.closest('.filepond--file-action-button, .filepond--drop-label, input.filepond--browser') && \$el.querySelector('input.filepond--browser')?.click()",
            ])
            ->columnSpanFull()
            ->required($required);
    }
}
