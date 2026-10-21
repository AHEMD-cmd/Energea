<?php

namespace App\Http\Controllers;

use App\Models\Client;
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

        return view('pages.clients', [
            'clients' => $clients,
            'page' => 'clients',
            'seo' => Seo::make(__('clients.seo_title'), __('clients.seo_description')),
        ]);
    }
}
