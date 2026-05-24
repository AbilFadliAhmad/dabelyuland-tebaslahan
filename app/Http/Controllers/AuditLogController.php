<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Jika request dari Axios (Load Data & Filter)
        if ($request->wantsJson() || $request->ajax()) {
            $query = AuditLog::query();

            // Filter Tipe (Wajib ada nilainya dari FE)
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Filter Action (Wajib ada nilainya dari FE)
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            // Filter Tanggal
            if ($request->filled('date')) {
                $dateVal = $request->date;
                
                if ($dateVal === 'today') {
                    $query->whereDate('created_at', Carbon::today());
                } elseif ($dateVal === 'this_week') {
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($dateVal === 'this_month') {
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                } elseif ($dateVal === 'custom' && $request->filled('custom_date')) {
                    // Flatpickr Range format: "Y-m-d to Y-m-d"
                    $dates = explode(' - ', $request->custom_date);
                    if (count($dates) == 2) {
                        $startDate = $dates[0] . ' 00:00:00';
                        $endDate = $dates[1] . ' 23:59:59';
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    } else {
                        // Jika hanya pilih 1 hari di custom
                        $query->whereDate('created_at', $dates[0]);
                    }
                }
            }

            // Kursor Pagination (Load More)
            if ($request->filled('cursor')) {
                $query->where('id', '<', $request->cursor);
            }

            $logs = $query->orderBy('id', 'desc')->limit(15)->get();

            return response()->json([
                'data' => $logs,
                'next_cursor' => $logs->last() ? $logs->last()->id : null,
                'has_more' => $logs->count() === 15 
            ]);
        }

        // 2. Jika request biasa (Akses halaman awal)
        return view('admin.audit.index');
    }
}