<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class IslamicFinanceController extends Controller
{
    public function index(): View
    {
        return view('islamic-finance.index');
    }
}
