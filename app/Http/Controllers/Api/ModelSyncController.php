<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ModelSyncController extends Controller
{
    public function receiveModel(Request $request)
    {
        // 1. Validasi memastikan kedua file dikirim dengan benar oleh script Python
        if (!$request->hasFile('onnx_model') || !$request->hasFile('meta_model')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sinkronisasi gagal. File model (.onnx) atau metadata (.json) tidak ditemukan dalam request.'
            ], 400);
        }

        // 2. Tentukan target lokasi folder ke public/model
        $targetDirectory = public_path('models');

        // Jika folder 'model' belum ada di dalam folder public, buat otomatis
        if (!File::exists($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true);
        }

        try {
            // 3. Ambil file dari request payload
            $onnxFile = $request->file('onnx_model');
            $metaFile = $request->file('meta_model');

            // 4. Pindahkan file ke target directory dengan nama yang konstan (menimpa file lama)
            // Fungsi move() di Laravel otomatis me-replace file jika namanya sama persis
            $onnxFile->move($targetDirectory, 'rf_model.onnx');
            $metaFile->move($targetDirectory, 'meta_model.json');

            return response()->json([
                'status' => 'success',
                'message' => '🚀 [BACKEND] Berhasil menerima asset model terbaru dan menyimpannya di folder public/model!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menulis file ke direktori server: ' . $e->getMessage()
            ], 500);
        }
    }
}