<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PageContent;
use App\Support\ClientsContent;
use App\Support\Media;
use App\Support\Seo;
use Illuminate\Contracts\View\View;

class ClientsController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        $content = PageContent::localized(ClientsContent::PAGE);


        return view('pages.clients', [
            'clients' => $clients,
            'clientsPage' => $content,
            'heroImage' => Media::url($content['hero']['image'], 'assets/images/about-banner.jpeg'),
            'finalImage' => Media::url($content['final']['image'], 'assets/images/projects/damac-data-center.webp'),
            'page' => 'clients',
            'seo' => Seo::make(__('clients.seo_title'), __('clients.seo_description')),
        ]);
    }
}
