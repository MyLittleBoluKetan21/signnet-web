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

        $publicModelsDirectory = public_path('models');         
        $storageMetadataDirectory = storage_path('app/ai_metadata'); 

        if (!File::exists($publicModelsDirectory)) {
            File::makeDirectory($publicModelsDirectory, 0755, true);
        }

        if (!File::exists($storageMetadataDirectory)) {
            File::makeDirectory($storageMetadataDirectory, 0755, true);
        }

        try {
            $onnxFile = $request->file('onnx_model');
            $metaFile = $request->file('meta_model');
            $labelsFile = $request->file('labels');

            $onnxFile->move($publicModelsDirectory, 'rf_model.onnx');
            $labelsFile->move($publicModelsDirectory, 'labels.json'); // <-- Sudah benar masuk folder public/models/

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