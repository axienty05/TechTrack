<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MServiceInternal;

class ServiceInternalPrintController extends Controller
{
    public function print(Request $request)
    {
        $user = auth()->user();
        $year = $request->query('year');
        $status = $request->query('status');
        $search = $request->query('search');

        $query = MServiceInternal::with(['barang', 'pemakai.department'])
            ->orderBy('tgl_service', 'asc');

        if (!empty($year)) {
            $query->whereYear('tgl_service', $year);
            $periodLabel = "Tahun " . $year;
        } else {
            $periodLabel = "Semua Periode";
        }

        if ($status === 'selesai') {
            $query->whereNotNull('tgl_selesai');
        } elseif ($status === 'proses') {
            $query->whereNull('tgl_selesai');
        }

        if (!empty($search)) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('kerusakan', 'like', $term)
                  ->orWhereHas('barang', fn ($bq) => $bq->where('nama_barang', 'like', $term)->orWhere('kode_barang', 'like', $term))
                  ->orWhereHas('pemakai', fn ($pq) => $pq->where('nama', 'like', $term));
            });
        }

        $services = $query->get();

        $stats = [
            'total'   => $services->count(),
            'selesai' => $services->whereNotNull('tgl_selesai')->count(),
            'proses'  => $services->whereNull('tgl_selesai')->count(),
        ];

        return view('service-internals.print', compact('services', 'user', 'periodLabel', 'stats', 'year', 'status'));
    }
}
