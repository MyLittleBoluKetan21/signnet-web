<?php

namespace App\Http\Controllers;

use App\Models\Datasets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SignController extends Controller
{
    private string $flaskUrl;

    public function __construct()
    {
        // 1. Mengarah langsung ke URL publik terowongan Localtunnel Google Colab kamu
        $this->flaskUrl = 'https://signnet-ai-skripsi-anda.loca.lt';
    }

    // =========================================================================
    // BARU & MODIFIKASI: Menerima file biner dan JSON kiriman dari Flask secara internal
    // Tambahkan penanda session/waktu agar polling tahu ada file baru masuk
    // =========================================================================
    public function updateModelFiles(Request $request)
    {
        // Beri waktu dan memori ekstra untuk memproses file besar
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $request->validate([
                'onnx_model' => 'required|file',
                'meta_model' => 'required|file',
                'labels'     => 'required|file',
            ]);

            $destinationPath = public_path('models');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Gunakan storeAs atau move dengan pengecekan
            $request->file('onnx_model')->move($destinationPath, 'rf_model.onnx');
            $request->file('labels')->move($destinationPath, 'labels.json');

            $storageMetadataDirectory = storage_path('app/ai_metadata');
            if (!File::exists($storageMetadataDirectory)) {
                File::makeDirectory($storageMetadataDirectory, 0755, true);
            }
            $request->file('meta_model')->move($storageMetadataDirectory, 'meta_model.json');

            // Kasih tanda ke session kalau proses simpan file training yang baru telah SELESAI
            session(['training_just_finished' => true]);

            return response()->json(['status' => 'success', 'message' => 'Update berhasil'], 200);

        } catch (\Exception $e) {
            Log::error('updateModelFiles error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // TUGAS BARU: Endpoint khusus yang dicari oleh fungsi polling JavaScript Anda
    // =========================================================================
    public function getLatestEvaluation()
    {
        try {
            $filePath = storage_path('app/ai_metadata/meta_model.json');

            // Cek apakah file meta_model.json ada dan session menandakan training baru saja selesai
            if (File::exists($filePath) && session('training_just_finished') === true) {
                $jsonContent = File::get($filePath);
                $data = json_decode($jsonContent, true);

                // Hapus tanda session agar polling berikutnya tidak mengira ini file baru lagi
                session()->forget('training_just_finished');

                return response()->json([
                    'status' => 'ready',
                    'accuracy' => $data['accuracy'] ?? 0.0,
                    'classification_report' => $data['classification_report'] ?? []
                ], 200);
            }

            // Jika file belum di-update / Google Colab masih memproses training
            return response()->json([
                'status' => 'pending',
                'message' => 'Model masih dalam proses pelatihan di Google Colab...'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membaca file evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // JANGAN DIUBAH: Khusus melayani loadInitialData() di trainmodel.blade.php
    // =========================================================================
    public function getModelStats()
    {
        try {
            $totalData   = Datasets::count();
            $totalLabels = Datasets::distinct('label')->count('label');
            $labelStats = Datasets::selectRaw('label, COUNT(*) as jumlah')
                ->groupBy('label')
                ->orderBy('label')
                ->pluck('jumlah', 'label');

            return response()->json([
                'status'       => 'success',
                'total'        => $totalData,
                'total_data'   => $totalData,
                'total_labels' => $totalLabels,
                'stats'        => $labelStats,
            ], 200);

        } catch (\Exception $e) {
            Log::error('getModelStats error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function collect(Request $request)
    {
        try {
            $request->validate([
                'label'    => 'required|string|max:50',
                'features' => 'required|array|size:126',
            ]);

            $adminId = 1;
            if (Auth::check()) {
                $user    = Auth::user();
                $adminId = $user?->id_admin ?? $user?->id ?? 1;
            }

            $featuresData = $request->input('features');

            Datasets::create([
                'admin_id' => $adminId,
                'features' => $featuresData, 
                'label'    => strtoupper(trim($request->input('label'))),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Data koordinat berhasil disimpan.',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error('collect error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // PERBAIKAN: Diubah menjadi pemicu Asinkronus agar terhindar dari timeout 503 Railway
    // =========================================================================
    public function trainModel()
    {
        try {
            $totalData = Datasets::count();
            if ($totalData < 5) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Dataset terlalu sedikit ({$totalData} sampel). Minimal 5 sampel diperlukan.",
                ], 400);
            }

            // Pastikan flag session dibersihkan saat tombol training baru ditekan
            session(['training_just_finished' => false]);

            // Tembak Google Colab dengan timeout sangat tipis (2 detik). 
            try {
                Http::timeout(2)->post("{$this->flaskUrl}/train-cloud");
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Abaikan timeout koneksi pendek karena sinyal pemicu sudah masuk ke Flask
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Proses training berhasil dipicu di Cloud Server! Hasilnya akan dikirim otomatis ke server setelah selesai.',
                'async'   => true
            ], 200);

        } catch (\Exception $e) {
            Log::error('trainModel error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getModelMetrics()
    {
        try {
            $response = Http::timeout(5)->get("{$this->flaskUrl}/api/get_model_stats");

            if ($response->successful()) {
                return response()->json($response->json(), 200);
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Model belum pernah di-training.',
                ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Server Python Cloud offline.',
            ], 503);
        }
    }

    // =========================================================================
    // Fungsi khusus yang digabungkan untuk halaman dashboard.blade.php
    // =========================================================================
    public function getDashboardStats()
    {
        try {
            $totalData   = Datasets::count();
            $totalLabels = Datasets::distinct('label')->count('label');
            $labelStats  = Datasets::selectRaw('label, COUNT(*) as jumlah')
                ->groupBy('label')
                ->orderBy('label')
                ->pluck('jumlah', 'label');

            $accuracy = 0.0;
            $confusionMatrix = [];
            
            $classificationReport = [];
            foreach ($labelStats as $label => $jumlah) {
                $classificationReport[$label] = ['support' => $jumlah];
            }

            try {
                $response = Http::timeout(5)->get("{$this->flaskUrl}/api/get_model_stats");
                
                if ($response->successful()) {
                    $flaskData = $response->json();
                    $accuracy = $flaskData['accuracy'] ?? 0.0;
                    $confusionMatrix = $flaskData['confusion_matrix'] ?? [];
                    
                    if (!empty($flaskData['classification_report'])) {
                        foreach ($flaskData['classification_report'] as $label => $metrics) {
                            if (isset($classificationReport[$label])) {
                                $classificationReport[$label] = array_merge($metrics, [
                                    'support' => $classificationReport[$label]['support']
                                ]);
                            } else {
                                $classificationReport[$label] = $metrics;
                            }
                        }
                    }
                }
            } catch (\Exception $flaskEx) {
                Log::warning('Dashboard memuat data saat Flask offline: ' . $flaskEx->getMessage());
            }

            return response()->json([
                'status'                => 'success',
                'total'                 => $totalData,
                'total_data'            => $totalData,
                'total_labels'          => $totalLabels,
                'stats'                 => $labelStats,
                'accuracy'              => $accuracy,
                'confusion_matrix'      => $confusionMatrix,
                'classification_report' => $classificationReport,
            ], 200);

        } catch (\Exception $e) {
            Log::error('getDashboardStats error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memuat data statistik dashboard: ' . $e->getMessage(),
            ], 500);
        }
    }
}