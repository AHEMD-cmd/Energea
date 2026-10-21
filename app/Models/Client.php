<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'logo', 'logo_code', 'is_published', 'sort_order'])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory, HasTranslations;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
