<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictController extends Controller
{
    public function predict(Request $request)
    {
        try {

            $features = $request->input('features');

            if (!$features || count($features) != 126) {

                return response()->json([
                    'label' => '-',
                    'probability' => 0
                ]);

            }

            $response = Http::timeout(3)
                ->post(
                    'http://127.0.0.1:5000/api/predict',
                    [
                        'features' => $features
                    ]
                );

            if ($response->failed()) {

                return response()->json([
                    'label' => '-',
                    'probability' => 0
                ]);

            }

            return response()->json(
                $response->json()
            );

        } catch (\Exception $e) {

            return response()->json([
                'label' => '-',
                'probability' => 0
            ]);

        }
    }
}