<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockOpname;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\ProductBatch;

class ReportController extends Controller
{
    // ============================================================
    // PREVIEW HTML
    // ============================================================

    public function stock(Request $request)
    {
        $items = Product::query()
            ->with(['category', 'unit', 'location'])
            ->when($request->filter === 'aman',    fn ($q) => $q->safeStock())
            ->when($request->filter === 'menipis', fn ($q) => $q->lowStock())
            ->when($request->filter === 'habis',   fn ($q) => $q->outOfStock())
            ->orderBy('name')
            ->get();

        return view('reports.stock', compact('items'));
    }

    public function in(Request $request)
    {
        $items = StockTransaction::with(['details.product.unit', 'supplier', 'user'])
            ->ofType(StockTransaction::TYPE_IN)
            ->betweenDate($request->from, $request->to)
            ->when($request->supplier_id, fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->latest('date')
            ->get();

        $suppliers = Supplier::orderBy('name')->get();

        return view('reports.in', compact('items', 'suppliers'));
    }

    public function out(Request $request)
    {
        $items = StockTransaction::with(['details.product.unit', 'user'])
            ->ofType(StockTransaction::TYPE_OUT)
            ->betweenDate($request->from, $request->to)
            ->when($request->recipient, fn ($q) => $q->where('recipient', 'like', "%{$request->recipient}%"))
            ->latest('date')
            ->get();

        return view('reports.out', compact('items'));
    }

    public function opname(Request $request)
    {
        $items = StockOpname::with(['details.product.unit', 'user'])
            ->betweenDate($request->from, $request->to)
            ->latest('date')
            ->get();

        return view('reports.opname', compact('items'));
    }

    public function adjustment(Request $request)
    {
        $items = StockAdjustment::with(['product.unit', 'user'])
            ->betweenDate($request->from, $request->to)
            ->latest('date')
            ->get();

        return view('reports.adjustment', compact('items'));
    }

    // ============================================================
    // EXPORT CSV
    // ============================================================

    public function exportStock(): StreamedResponse
    {
        $products = Product::with(['category', 'unit', 'location'])->orderBy('name')->get();

        $rows = $products->map(fn ($p) => [
            $p->code,
            $p->name,
            $p->category?->name,
            $p->unit?->symbol,
            $p->location?->name,
            $p->stock,
            $p->min_stock,
            strtoupper($p->stock_status),
        ]);

        return $this->streamCsv(
            'laporan-stok-' . now()->format('Ymd-His') . '.csv',
            ['Kode', 'Nama', 'Kategori', 'Satuan', 'Lokasi', 'Stok', 'Min', 'Status'],
            $rows
        );
    }

    public function exportIn(Request $request): StreamedResponse
    {
        $transactions = StockTransaction::with(['details.product.unit', 'supplier'])
            ->ofType(StockTransaction::TYPE_IN)
            ->betweenDate($request->from, $request->to)
            ->latest('date')
            ->get();

        $rows = $transactions->flatMap(fn ($t) =>
            $t->details->map(fn ($d) => [
                $t->code,
                $t->date->format('Y-m-d'),
                $t->supplier?->name,
                $t->delivery_note,
                $d->product->code,
                $d->product->name,
                $d->qty,
                $d->product->unit?->symbol,
                $t->user?->name,
            ])
        );

        return $this->streamCsv(
            'laporan-masuk-' . now()->format('Ymd-His') . '.csv',
            ['Kode Transaksi', 'Tanggal', 'Supplier', 'No. SJ', 'Kode Barang', 'Nama Barang', 'Qty', 'Satuan', 'User'],
            $rows
        );
    }

    public function exportOut(Request $request): StreamedResponse
    {
        $transactions = StockTransaction::with(['details.product.unit'])
            ->ofType(StockTransaction::TYPE_OUT)
            ->betweenDate($request->from, $request->to)
            ->latest('date')
            ->get();

        $rows = $transactions->flatMap(fn ($t) =>
            $t->details->map(fn ($d) => [
                $t->code,
                $t->date->format('Y-m-d'),
                $t->recipient,
                $t->purpose,
                $d->product->code,
                $d->product->name,
                $d->qty,
                $d->product->unit?->symbol,
                $t->user?->name,
            ])
        );

        return $this->streamCsv(
            'laporan-keluar-' . now()->format('Ymd-His') . '.csv',
            ['Kode Transaksi', 'Tanggal', 'Penerima', 'Keperluan', 'Kode Barang', 'Nama Barang', 'Qty', 'Satuan', 'User'],
            $rows
        );
    }

    public function exportOpname(Request $request): StreamedResponse
    {
        $opnames = StockOpname::with(['details.product.unit'])
            ->betweenDate($request->from, $request->to)
            ->latest('date')
            ->get();

        $rows = $opnames->flatMap(fn ($so) =>
            $so->details->map(fn ($d) => [
                $so->code,
                $so->date->format('Y-m-d'),
                $d->product->code,
                $d->product->name,
                $d->system_stock,
                $d->physical_stock,
                $d->difference,
                $d->difference_label,
                $d->product->unit?->symbol,
            ])
        );

        return $this->streamCsv(
            'laporan-opname-' . now()->format('Ymd-His') . '.csv',
            ['Kode SO', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Stok Sistem', 'Stok Fisik', 'Selisih', 'Keterangan', 'Satuan'],
            $rows
        );
    }

    public function exportAdjustment(Request $request): StreamedResponse
    {
        $items = StockAdjustment::with(['product.unit', 'user'])
            ->betweenDate($request->from, $request->to)
            ->latest('date')
            ->get();

        $rows = $items->map(fn ($a) => [
            $a->code,
            $a->date->format('Y-m-d'),
            $a->product->code,
            $a->product->name,
            $a->formatted_adjustment,
            $a->product->unit?->symbol,
            $a->reason,
            $a->reference,
            $a->user?->name,
        ]);

        return $this->streamCsv(
            'laporan-penyesuaian-' . now()->format('Ymd-His') . '.csv',
            ['Kode', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Penyesuaian', 'Satuan', 'Alasan', 'Referensi', 'User'],
            $rows
        );
    }

    // ============================================================
    // HELPER
    // ============================================================

    /**
     * Stream CSV dengan BOM UTF-8 supaya Excel bisa baca karakter dengan benar.
     */
    private function streamCsv(string $filename, array $header, $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');

            // BOM UTF-8 untuk Excel
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, $header);

            foreach ($rows as $row) {
                fputcsv($out, (array) $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ============================================================
// LAPORAN EXPIRED
// ============================================================

public function expired(Request $request)
{
    $filter = $request->filter ?? 'segera'; // segera, expired, semua

    $query = ProductBatch::with(['product.unit', 'product.category'])
        ->whereNotNull('expired_date')
        ->where('qty_remaining', '>', 0);

    if ($filter === 'segera') {
        $query->whereDate('expired_date', '>=', now())
              ->whereDate('expired_date', '<=', now()->addDays(30));
    } elseif ($filter === 'expired') {
        $query->whereDate('expired_date', '<', now());
    }

    $items = $query->orderBy('expired_date')->get();

    return view('reports.expired', compact('items', 'filter'));
}

public function exportExpired(Request $request): StreamedResponse
{
    $filter = $request->filter ?? 'segera';

    $query = ProductBatch::with('product.unit')
        ->whereNotNull('expired_date')
        ->where('qty_remaining', '>', 0);

    if ($filter === 'segera') {
        $query->whereDate('expired_date', '>=', now())
              ->whereDate('expired_date', '<=', now()->addDays(30));
    } elseif ($filter === 'expired') {
        $query->whereDate('expired_date', '<', now());
    }

    $rows = $query->orderBy('expired_date')->get()->map(fn ($b) => [
        $b->batch_code,
        $b->product->code,
        $b->product->name,
        $b->entry_date->format('Y-m-d'),
        $b->expired_date?->format('Y-m-d'),
        $b->qty_initial,
        $b->qty_remaining,
        $b->product->unit?->symbol,
        $b->days_until_expired,
        strtoupper($b->expiry_status),
    ]);

    return $this->streamCsv(
        'laporan-expired-' . now()->format('Ymd-His') . '.csv',
        ['Kode Batch', 'Kode Barang', 'Nama Barang', 'Tgl Masuk', 'Tgl Expired', 'Qty Awal', 'Qty Sisa', 'Satuan', 'Hari Tersisa', 'Status'],
        $rows
    );
}

// ============================================================
// LAPORAN BATCH
// ============================================================

public function batch(Request $request)
{
    $query = ProductBatch::with(['product.unit', 'product.category'])
        ->when($request->product_id, fn ($q) => $q->where('product_id', $request->product_id))
        ->when($request->from, fn ($q) => $q->whereDate('entry_date', '>=', $request->from))
        ->when($request->to,   fn ($q) => $q->whereDate('entry_date', '<=', $request->to))
        ->when($request->status === 'available', fn ($q) => $q->where('qty_remaining', '>', 0))
        ->when($request->status === 'empty',     fn ($q) => $q->where('qty_remaining', '<=', 0));

    $items = $query->orderByDesc('entry_date')->paginate(30)->withQueryString();

    $products = Product::where('has_batch', true)->orderBy('name')->get();

    return view('reports.batch', compact('items', 'products'));
}

public function exportBatch(Request $request): StreamedResponse
{
    $items = ProductBatch::with(['product.unit'])
        ->when($request->product_id, fn ($q) => $q->where('product_id', $request->product_id))
        ->when($request->from, fn ($q) => $q->whereDate('entry_date', '>=', $request->from))
        ->when($request->to,   fn ($q) => $q->whereDate('entry_date', '<=', $request->to))
        ->orderByDesc('entry_date')
        ->get();

    $rows = $items->map(fn ($b) => [
        $b->batch_code,
        $b->product->code,
        $b->product->name,
        $b->entry_date->format('Y-m-d'),
        $b->expired_date?->format('Y-m-d') ?? '-',
        $b->qty_initial,
        $b->qty_remaining,
        $b->product->unit?->symbol,
        $b->price,
        $b->source_type,
    ]);

    return $this->streamCsv(
        'laporan-batch-' . now()->format('Ymd-His') . '.csv',
        ['Kode Batch', 'Kode Barang', 'Nama Barang', 'Tgl Masuk', 'Tgl Expired', 'Qty Awal', 'Qty Sisa', 'Satuan', 'Harga', 'Sumber'],
        $rows
    );
}
}