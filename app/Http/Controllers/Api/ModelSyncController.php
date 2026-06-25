<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ModelSyncController extends Controller
{
    public function receiveModel(Request $request)
    {
        // 1. Validasi memastikan ketiga file dikirim dengan benar oleh script Python
        if (!$request->hasFile('onnx_model') || !$request->hasFile('meta_model') || !$request->hasFile('labels')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sinkronisasi gagal. File model (.onnx), metadata (.json), atau labels (.json) tidak ditemukan dalam request.'
            ], 400);
        }

        // 2. Tentukan target lokasi folder asset
        $publicModelsDirectory = public_path('models');         // Untuk Frontend (Akses Cepat Browser)
        $storageMetadataDirectory = storage_path('app/ai_metadata'); // Untuk Backend / Dashboard Admin (Aman)

        // Buat folder public/models jika belum ada
        if (!File::exists($publicModelsDirectory)) {
            File::makeDirectory($publicModelsDirectory, 0755, true);
        }

        // Buat folder storage/app/ai_metadata jika belum ada
        if (!File::exists($storageMetadataDirectory)) {
            File::makeDirectory($storageMetadataDirectory, 0755, true);
        }

        try {
            // 3. Ambil file dari request payload
            $onnxFile = $request->file('onnx_model');
            $metaFile = $request->file('meta_model');
            $labelsFile = $request->file('labels');

            // 4. Pindahkan file ke target masing-masing (Otomatis menimpa file lama)
            // rf_model.onnx dan labels.json masuk ke folder PUBLIC
            $onnxFile->move($publicModelsDirectory, 'rf_model.onnx');
            $labelsFile->move($publicModelsDirectory, 'labels.json');

            // meta_model.json masuk ke folder STORAGE (Aman dari load browser frontend)
            $metaFile->move($storageMetadataDirectory, 'meta_model.json');

            return response()->json([
                'status' => 'success',
                'message' => '🚀 [BACKEND] Berhasil menerima seluruh asset model terbaru. rf_model.onnx & labels.json disinkronkan ke public, meta_model.json disimpan di storage aman!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menulis file ke direktori server: ' . $e->getMessage()
            ], 500);
        }
    }
}