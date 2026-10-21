<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'number',
    'image',
    'title',
    'type',
    'summary',
    'overview',
    'steps',
    'facts',
    'seo_title',
    'seo_description',
    'is_published',
    'sort_order',
])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory, HasTranslations;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'array',
            'type' => 'array',
            'summary' => 'array',
            'overview' => 'array',
            'steps' => 'array',
            'facts' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<ServiceGroup, $this>
     */
    public function groups(): HasMany
    {
        return $this->hasMany(ServiceGroup::class)->orderBy('sort_order');
    }
}
