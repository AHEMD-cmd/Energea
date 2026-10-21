<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use App\Models\PageContent;
use App\Support\CertificatesContent;
use App\Support\Media;

class CertificatesController extends Controller
{
    public function index(): View
    {
        $certificates = Certificate::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        $content = PageContent::localized(CertificatesContent::PAGE);


        return view('pages.certificates', [
            'certificates' => $certificates,
            'certificatesPage' => $content,
            'heroImage' => Media::url($content['hero']['image'], 'assets/images/services-banner.jpeg'),
            'page' => 'certificates',
            'seo' => Seo::make(__('certificates.seo_title'), __('certificates.seo_description')),
        ]);
    }
}
