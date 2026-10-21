<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;

class ClamavScanner
{
    public function scan(?UploadedFile $file): bool
    {
        if (! $file || ! config('quote.clamav.enabled')) {
            return true;
        }

        $path = $file->getRealPath();
        if (! $path) {
            return true;
        }

        $binary = config('quote.clamav.path');
        $result = Process::run([$binary, '--no-summary', $path]);

        return $result->successful();
    }
}
