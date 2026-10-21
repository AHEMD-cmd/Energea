<?php

namespace App\Models;

use App\Enums\PartyType;
use App\Models\Concerns\HasTranslations;
use Database\Factories\ProjectPartyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'party_type', 'name', 'logo', 'sort_order'])]
class ProjectParty extends Model
{
    /** @use HasFactory<ProjectPartyFactory> */
    use HasFactory, HasTranslations;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'party_type' => PartyType::class,
            'name' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
