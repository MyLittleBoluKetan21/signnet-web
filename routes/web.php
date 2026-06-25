<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignController;
use App\Http\Controllers\PredictController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ManageAdminController;
use App\Http\Controllers\Api\ModelSyncController;
use Illuminate\Support\Facades\File;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/recognition', function () {
    return view('recognition');
})->name('recognition');

Route::post('/predict', [PredictController::class, 'predict'])->name('predict');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth:admin'])->name('admin.dashboard');

Route::middleware('auth:admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['post', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/trainmodel', fn() => view('admin.trainmodel'))->name('admin.trainmodel');
    Route::get('/get-total-samples', [SignController::class, 'getModelStats'])->name('sign.stats');
    Route::post('/collect-data', [SignController::class, 'collect'])->name('sign.collect');
    Route::post('/train-model', [SignController::class, 'trainModel'])->name('sign.train');

    Route::get('/api/model-metrics', [SignController::class, 'getModelMetrics'])->name('sign.metrics');
    Route::get('/api/dashboard-stats', [SignController::class, 'getDashboardStats'])->name('admin.dashboard.stats');
    Route::post('/admin/update-model-files', [SignController::class, 'updateModelFiles'])->name('admin.update.files');

    Route::prefix('admin/dataset')->name('admin.dataset.')->group(function () {
        Route::get('/', [DatasetController::class, 'index'])->name('index');
        Route::get('/stats', [DatasetController::class, 'getStats'])->name('stats');
        Route::post('/delete-label/{label}', [DatasetController::class, 'deleteLabel'])->name('delete-label');
        Route::post('/clear', [DatasetController::class, 'clearDataset'])->name('clear');
        Route::get('/download', [DatasetController::class, 'download'])->name('download');
        Route::post('/import', [DatasetController::class, 'import'])->name('import');
    });

    Route::prefix('admin/kelola-admin')->name('admin.manage.')->group(function () {
        Route::get('/', [ManageAdminController::class, 'index'])->name('index');
        Route::get('/list', [ManageAdminController::class, 'list'])->name('list');
        Route::post('/store', [ManageAdminController::class, 'store'])->name('store');
        Route::post('/update/{id}', [ManageAdminController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ManageAdminController::class, 'destroy'])->name('delete');
    });
});

Route::post('/api/sync-model', [ModelSyncController::class, 'receiveModel']);


// =========================================================================
// OPTIMASI JALUR BELAKANG: TRANSFER ASSET ML VIA STORAGE + GZIP
// =========================================================================

// 1. Rute Khusus Pengambilan Model .onnx
Route::get('/models/rf_model.onnx', function () {
    $path = storage_path('app/models/rf_model.onnx'); 
    if (!File::exists($path)) abort(404);

    ob_start('ob_gzhandler'); 
    $response = response()->file($path, [
        'Content-Type' => 'application/octet-stream',
        'Cache-Control' => 'public, max-age=31536000, immutable'
    ]);
    ob_end_flush();

    return $response;
});

// 2. Rute Khusus Pengambilan Label Kelas .json
Route::get('/models/labels.json', function () {
    $path = storage_path('app/models/labels.json'); 
    if (!File::exists($path)) abort(404);

    ob_start('ob_gzhandler'); 
    $response = response()->file($path, [
        'Content-Type' => 'application/json',
        'Cache-Control' => 'public, max-age=31536000, immutable'
    ]);
    ob_end_flush();

    return $response;
});

require __DIR__.'/auth.php';