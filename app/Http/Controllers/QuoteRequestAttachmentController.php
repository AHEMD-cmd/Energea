<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuoteRequestAttachmentController extends Controller
{
    public function download(Request $request, QuoteRequest $quoteRequest): RedirectResponse|StreamedResponse
    {
        if (! $request->user()) {
            return redirect()->guest(route('filament.admin.auth.login'));
        }

        abort_unless((bool) $request->user()->is_admin, 403);

        $path = $quoteRequest->attachment_path;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download(
            $path,
            $quoteRequest->attachment_original_name ?: basename($path),
        );
    }
}
