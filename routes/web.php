<?php

use App\Http\Controllers\CertificatesController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'root'])->name('root');
Route::get('/index.html', [RedirectController::class, 'rootIndex'])->name('legacy.root-index');

Route::prefix('{locale}')
    ->whereIn('locale', config('energea.locales'))
    ->group(function (): void {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/about', [PageController::class, 'about'])->name('about');

        Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
        Route::get('/services/{slug}', [ServicesController::class, 'show'])->name('services.show');

        Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.index');
        Route::get('/projects/{slug}', [ProjectsController::class, 'show'])->name('projects.show');

        Route::get('/clients', [ClientsController::class, 'index'])->name('clients');
        Route::get('/certificates', [CertificatesController::class, 'index'])->name('certificates');
        Route::get('/contact', [ContactController::class, 'show'])->name('contact');

        Route::get('/index.html', [RedirectController::class, 'index'])->name('legacy.index');
        Route::get('/service.html', [RedirectController::class, 'service'])->name('legacy.service');
        Route::get('/project.html', [RedirectController::class, 'project'])->name('legacy.project');
        Route::get('/projects.html', [RedirectController::class, 'projects'])->name('legacy.projects');
        Route::get('/{page}.html', [RedirectController::class, 'page'])
            ->whereIn('page', ['about', 'services', 'clients', 'certificates', 'contact'])
            ->name('legacy.page');
    });
