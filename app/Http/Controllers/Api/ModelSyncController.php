<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ModelSyncController extends Controller
{
    public function receiveModel(Request $request)
    {
        if (!$request->hasFile('onnx_model') || !$request->hasFile('meta_model') || !$request->hasFile('labels')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sinkronisasi gagal. File model (.onnx), metadata (.json), atau labels (.json) tidak ditemukan dalam request.'
            ], 400);
        }

        // PERBAIKAN: Ubah folder tujuan .onnx & labels.json ke folder storage aman
        $secureModelsDirectory = storage_path('app/models');         
        $storageMetadataDirectory = storage_path('app/ai_metadata'); 

        if (!File::exists($secureModelsDirectory)) {
            File::makeDirectory($secureModelsDirectory, 0755, true);
        }

        if (!File::exists($storageMetadataDirectory)) {
            File::makeDirectory($storageMetadataDirectory, 0755, true);
        }

        try {
            $onnxFile = $request->file('onnx_model');
            $metaFile = $request->file('meta_model');
            $labelsFile = $request->file('labels');

            // Pindahkan file langsung ke folder storage terproteksi
            $onnxFile->move($secureModelsDirectory, 'rf_model.onnx');
            $labelsFile->move($secureModelsDirectory, 'labels.json'); 

            $metaFile->move($storageMetadataDirectory, 'meta_model.json');

            return response()->json([
                'status' => 'success',
                'message' => '🚀 [BACKEND] Berhasil menerima seluruh asset model terbaru di folder storage aman!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menulis file ke direktori server: ' . $e->getMessage()
            ], 500);
        }
    }
}