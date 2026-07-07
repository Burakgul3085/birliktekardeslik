<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Zakat\PriceService;
use App\Support\Zakat\ZakatSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ZakatController extends Controller
{
    public function index(): View
    {
        $settings = ZakatSettings::forPage();

        return view('zakat.index', [
            'settings' => $settings,
            'faqItems' => $settings['faq'],
            'featuredActivities' => Project::query()
                ->active()
                ->orderBy('sort_order')
                ->take(3)
                ->get(),
        ]);
    }

    public function prices(PriceService $priceService): JsonResponse
    {
        return response()->json($priceService->snapshot());
    }
}
