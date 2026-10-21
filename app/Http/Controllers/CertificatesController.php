<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Support\Seo;
use Illuminate\Contracts\View\View;

class CertificatesController extends Controller
{
    public function index(): View
    {
        $certificates = Certificate::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.certificates', [
            'certificates' => $certificates,
            'page' => 'certificates',
            'seo' => Seo::make(__('certificates.seo_title'), __('certificates.seo_description')),
        ]);
    }
}
