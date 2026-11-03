<?php

use App\Http\Controllers\CertificatesController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\QuoteRequestAttachmentController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


Route::get('/', [HomeController::class, 'root'])->name('root');
Route::get('/index.html', [RedirectController::class, 'rootIndex'])->name('legacy.root-index');

Route::get('/'.config('energea.admin_path').'/quote-requests/{quoteRequest}/attachment', [QuoteRequestAttachmentController::class, 'download'])
    ->name('admin.quote-requests.attachment');

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
        Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:10,1')->name('contact.store');
        Route::get('/contact/captcha', [ContactController::class, 'captcha'])->middleware('throttle:30,1')->name('contact.captcha');

        Route::get('/index.html', [RedirectController::class, 'index'])->name('legacy.index');
        Route::get('/service.html', [RedirectController::class, 'service'])->name('legacy.service');
        Route::get('/project.html', [RedirectController::class, 'project'])->name('legacy.project');
        Route::get('/projects.html', [RedirectController::class, 'projects'])->name('legacy.projects');
        Route::get('/{page}.html', [RedirectController::class, 'page'])
            ->whereIn('page', ['about', 'services', 'clients', 'certificates', 'contact'])
            ->name('legacy.page');
    });



Route::get('/test-mail', function () {
    try {
        $sent = Mail::raw('This is a test email from Laravel.', function ($message) {
            $message->to('you@example.com')
                    ->subject('Test Mail');
        });

        if ($sent) {
            return response()->json([
                'status'     => 'sent',
                'message_id' => $sent->getMessageId(),
            ]);
        }

        return response()->json(['status' => 'not sent'], 500);
    } catch (\Throwable $e) {
        Log::error('Test mail failed: ' . $e->getMessage());

        return response()->json([
            'status' => 'failed',
            'error'  => $e->getMessage(),
        ], 500);
    }
});
