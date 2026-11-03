<?php

namespace App\Models;

use App\Enums\QuoteRequestStatus;
use Database\Factories\QuoteRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'locale',
    'name',
    'company',
    'email',
    'phone',
    'service_id',
    'location_key',
    'location_other',
    'message',
    'attachment_path',
    'attachment_original_name',
    'status',
    'mail_sent_at',
    'mail_error',
])]
class QuoteRequest extends Model
{
    /** @use HasFactory<QuoteRequestFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => QuoteRequestStatus::class,
            'mail_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (QuoteRequest $quoteRequest): void {
            if ($quoteRequest->attachment_path) {
                Storage::disk('local')->delete($quoteRequest->attachment_path);
            }
        });
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
