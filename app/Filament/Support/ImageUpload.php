<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

class ImageUpload
{
    /**
     * An image field whose current preview doubles as the replace button.
     */
    public static function make(string $name, string $directory, bool $required = true): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->maxSize(5120)
            ->helperText('Click the image preview to replace it, or remove it to clear.')
            ->extraAttributes([
                'style' => 'cursor: pointer',
                'x-on:click' => "! \$event.target.closest('.filepond--file-action-button, .filepond--drop-label, input.filepond--browser') && \$el.querySelector('input.filepond--browser')?.click()",
            ])
            ->columnSpanFull()
            ->required($required);
    }
}
