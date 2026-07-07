<?php

namespace App\Http\Controllers;

use App\Support\Zakat\PriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ZakatController extends Controller
{
    public function index(): View
    {
        return view('zakat.index', [
            'settings' => [
                'nisap_grams' => (float) config('zakat.nisap_grams', 80),
                'nisap_karat' => (int) config('zakat.nisap_karat', 24),
                'zakat_rate' => (float) config('zakat.rate', 0.025),
                'karat_factors' => config('zakat.karat_factors', []),
            ],
        ]);
    }

    public function prices(PriceService $priceService): JsonResponse
    {
        return response()->json($priceService->snapshot());
    }
}
