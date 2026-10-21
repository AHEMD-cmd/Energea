<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\ServiceGroupItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['service_group_id', 'text', 'sort_order'])]
class ServiceGroupItem extends Model
{
    /** @use HasFactory<ServiceGroupItemFactory> */
    use HasFactory, HasTranslations;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'text' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<ServiceGroup, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class, 'service_group_id');
    }
}
