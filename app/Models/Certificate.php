<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'type', 'image', 'is_published', 'sort_order'])]
class Certificate extends Model
{
    /** @use HasFactory<CertificateFactory> */
    use HasFactory, HasTranslations;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'type' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
