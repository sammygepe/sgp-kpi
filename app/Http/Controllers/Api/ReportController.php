<?php

namespace App\Http\Controllers\Api;

use App\Models\TaskInstance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KPIReportExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    // === LAPORAN STAFF (JSON) ===
    public function staffReport(Request $request)
    {
        $period = $request->input('period', 'month');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $staffs = User::where('role', 'staff')->with('supervisor')->get();
        $result = [];

        foreach ($staffs as $staff) {
            $query = TaskInstance::where('assignee_id', $staff->id)
                ->where('status', 'completed')
                ->whereNotNull('skor_akhir');

            switch ($period) {
                case 'month':
                    $query->whereYear('updated_at', $year)->whereMonth('updated_at', $month);
                    break;
                case 'quarter':
                    $quarter = ceil($month / 3);
                    $startMonth = ($quarter - 1) * 3 + 1;
                    $endMonth = $quarter * 3;
                    $query->whereYear('updated_at', $year)
                        ->whereMonth('updated_at', '>=', $startMonth)
                        ->whereMonth('updated_at', '<=', $endMonth);
                    break;
                case 'year':
                    $query->whereYear('updated_at', $year);
                    break;
            }

            $completedTasks = $query->get();
            $avgScore = $completedTasks->avg('skor_akhir') ?? 0;
            $totalTasks = $completedTasks->count();

            $result[] = [
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'supervisor' => $staff->supervisor?->name ?? 'Tidak ada',
                'avg_score' => round($avgScore, 2),
                'total_tasks' => $totalTasks,
            ];
        }

        usort($result, fn($a, $b) => $b['avg_score'] <=> $a['avg_score']);

        return response()->json([
            'period' => $period,
            'year' => $year,
            'month' => $month,
            'data' => $result,
        ]);
    }

    // === DETAIL STAFF ===
    public function staffDetail($staffId, Request $request)
    {
        $staff = User::findOrFail($staffId);
        $tasks = TaskInstance::where('assignee_id', $staff->id)
            ->where('status', 'completed')
            ->whereNotNull('skor_akhir')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'staff' => $staff,
            'tasks' => $tasks,
            'summary' => [
                'total_tasks' => $tasks->count(),
                'avg_score' => $tasks->avg('skor_akhir') ?? 0,
                'max_score' => $tasks->max('skor_akhir') ?? 0,
                'min_score' => $tasks->min('skor_akhir') ?? 0,
            ]
        ]);
    }

    // === EKSPOR PDF ===
    public function exportPdf(Request $request)
    {
        $period = $request->input('period', 'month');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $staffs = User::where('role', 'staff')->with('supervisor')->get();
        $result = [];

        foreach ($staffs as $staff) {
            $query = TaskInstance::where('assignee_id', $staff->id)
                ->where('status', 'completed')
                ->whereNotNull('skor_akhir');

            switch ($period) {
                case 'month':
                    $query->whereYear('updated_at', $year)->whereMonth('updated_at', $month);
                    break;
                case 'quarter':
                    $quarter = ceil($month / 3);
                    $startMonth = ($quarter - 1) * 3 + 1;
                    $endMonth = $quarter * 3;
                    $query->whereYear('updated_at', $year)
                        ->whereMonth('updated_at', '>=', $startMonth)
                        ->whereMonth('updated_at', '<=', $endMonth);
                    break;
                case 'year':
                    $query->whereYear('updated_at', $year);
                    break;
            }

            $completedTasks = $query->get();
            $avgScore = $completedTasks->avg('skor_akhir') ?? 0;
            $totalTasks = $completedTasks->count();

            $result[] = [
                'staff_name' => $staff->name,
                'supervisor' => $staff->supervisor?->name ?? 'Tidak ada',
                'avg_score' => round($avgScore, 2),
                'total_tasks' => $totalTasks,
            ];
        }

        usort($result, fn($a, $b) => $b['avg_score'] <=> $a['avg_score']);

        $data = [
            'period' => $period,
            'year' => $year,
            'month' => $month,
            'data' => $result,
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.kpi-pdf', $data);
        return $pdf->download('laporan-kpi-' . now()->format('Y-m-d') . '.pdf');
    }

    // === EKSPOR EXCEL ===
    public function exportExcel(Request $request)
    {
        $period = $request->input('period', 'month');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $staffs = User::where('role', 'staff')->with('supervisor')->get();
        $result = [];

        foreach ($staffs as $staff) {
            $query = TaskInstance::where('assignee_id', $staff->id)
                ->where('status', 'completed')
                ->whereNotNull('skor_akhir');

            switch ($period) {
                case 'month':
                    $query->whereYear('updated_at', $year)->whereMonth('updated_at', $month);
                    break;
                case 'quarter':
                    $quarter = ceil($month / 3);
                    $startMonth = ($quarter - 1) * 3 + 1;
                    $endMonth = $quarter * 3;
                    $query->whereYear('updated_at', $year)
                        ->whereMonth('updated_at', '>=', $startMonth)
                        ->whereMonth('updated_at', '<=', $endMonth);
                    break;
                case 'year':
                    $query->whereYear('updated_at', $year);
                    break;
            }

            $completedTasks = $query->get();
            $avgScore = $completedTasks->avg('skor_akhir') ?? 0;
            $totalTasks = $completedTasks->count();

            $result[] = [
                'Nama Staf' => $staff->name,
                'Atasan' => $staff->supervisor?->name ?? 'Tidak ada',
                'Rata-rata Skor' => round($avgScore, 2),
                'Total Tugas' => $totalTasks,
            ];
        }

        usort($result, fn($a, $b) => $b['Rata-rata Skor'] <=> $a['Rata-rata Skor']);

        return Excel::download(new KPIReportExport($result), 'laporan-kpi-' . now()->format('Y-m-d') . '.xlsx');
    }
}
