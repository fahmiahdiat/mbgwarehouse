<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik kartu atas
        $totalBarang = Product::active()->count();
        $stokMenipis = Product::active()->lowStock()->count();
        $stokHabis   = Product::active()->outOfStock()->count();

        // Aktivitas hari ini
        $today = now()->toDateString();
        $masukHariIni  = StockTransaction::ofType(StockTransaction::TYPE_IN)->whereDate('date', $today)->count();
        $keluarHariIni = StockTransaction::ofType(StockTransaction::TYPE_OUT)->whereDate('date', $today)->count();
        $soHariIni     = StockOpname::whereDate('date', $today)->count();

        // Peringatan stok
        $peringatan = Product::active()
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(10)
            ->get();

        // Grafik 7 hari terakhir
        $labels  = [];
        $dataIn  = [];
        $dataOut = [];

        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i);
            $labels[]  = $d->format('d/m');
            $dataIn[]  = StockTransaction::ofType(StockTransaction::TYPE_IN)
                ->whereDate('date', $d)->count();
            $dataOut[] = StockTransaction::ofType(StockTransaction::TYPE_OUT)
                ->whereDate('date', $d)->count();
        }

        return view('dashboard', compact(
            'totalBarang',
            'stokMenipis',
            'stokHabis',
            'masukHariIni',
            'keluarHariIni',
            'soHariIni',
            'peringatan',
            'labels',
            'dataIn',
            'dataOut'
        ));
    }
}