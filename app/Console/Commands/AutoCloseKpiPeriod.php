<?php

namespace App\Console\Commands;

use App\Models\KpiPeriod;
use App\Models\KpiSetting;
use App\Models\TaskInstance;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCloseKpiPeriod extends Command
{
    protected $signature = 'kpi:auto-close';
    protected $description = 'Tutup periode KPI otomatis setelah grace period habis';

    public function handle()
    {
        $this->info('🔄 Memulai auto-close periode KPI...');

        // Cek apakah auto-close diaktifkan
        $setting = KpiSetting::first();
        if (!$setting || !$setting->auto_close_enabled) {
            $this->warn('⚠️  Auto-close dimatikan di kpi_settings. Skip.');
            return;
        }

        $defaultGrace = $setting->grace_period_days ?? 30;
        $decemberGrace = $setting->grace_period_december_days ?? 7;

        // Cek 6 bulan terakhir
        $monthsToCheck = $this->getMonthsToCheck();

        $closedCount = 0;

        foreach ($monthsToCheck as $period) {
            $year = $period['year'];
            $month = $period['month'];

            // Cek apakah periode sudah ada & sudah closed
            $existingPeriod = KpiPeriod::where('year', $year)
                ->where('month', $month)
                ->first();

            if ($existingPeriod && $existingPeriod->status === 'closed') {
                continue;
            }

            // Tentukan grace period
            $graceDays = ($month === 12) ? $decemberGrace : $defaultGrace;

            // Hitung tanggal tutup
            $closeDate = Carbon::create($year, $month, 1)
                ->endOfMonth()
                ->addDays($graceDays);

            // Kalau belum waktunya tutup, skip
            if (now()->lt($closeDate)) {
                continue;
            }

            // Cek apakah ada tugas di bulan ini
            $taskCount = $this->countTasksForPeriod($year, $month);

            if ($taskCount === 0) {
                $this->line("  ⏭️  {$year}-{$month}: Tidak ada tugas. Skip.");
                continue;
            }

            // Tutup periode
            $this->closePeriod($year, $month, $graceDays);

            $this->info("  ✅ {$year}-{$month}: {$taskCount} tugas ditutup.");
            $closedCount++;
        }

        if ($closedCount === 0) {
            $this->info('ℹ️  Tidak ada periode yang perlu ditutup.');
        } else {
            $this->info("🎉 Selesai. {$closedCount} periode ditutup.");
        }
    }

    /**
     * Daftar bulan yang perlu dicek (6 bulan terakhir)
     */
    private function getMonthsToCheck(): array
    {
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $months[] = [
                'year' => $date->year,
                'month' => $date->month,
            ];
        }
        return $months;
    }

    /**
     * Hitung jumlah tugas di periode tertentu
     */
    private function countTasksForPeriod(int $year, int $month): int
    {
        return TaskInstance::where(function ($query) use ($year, $month) {
            $query->where(function ($q) use ($year, $month) {
                $q->whereYear('deadline', $year)
                    ->whereMonth('deadline', $month);
            });
            $query->orWhere(function ($q) use ($year, $month) {
                $q->whereYear('tanggal_submit', $year)
                    ->whereMonth('tanggal_submit', $month);
            });
        })->whereNull('kpi_period_id')->count();
    }

    /**
     * Tutup periode: create KPI period + update tasks
     */
    private function closePeriod(int $year, int $month, int $graceDays): void
    {
        // 1. Create atau update periode
        $period = KpiPeriod::updateOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'grace_period_days' => $graceDays,
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => null, // otomatis
            ]
        );

        // 2. Update kpi_period_id untuk tugas di bulan ini
        TaskInstance::whereNull('kpi_period_id')
            ->where(function ($query) use ($year, $month) {
                $query->where(function ($q) use ($year, $month) {
                    $q->whereYear('deadline', $year)
                        ->whereMonth('deadline', $month);
                });
                $query->orWhere(function ($q) use ($year, $month) {
                    $q->whereYear('tanggal_submit', $year)
                        ->whereMonth('tanggal_submit', $month);
                });
            })
            ->update(['kpi_period_id' => $period->id]);

        // 3. Tugas submitted → completed (nilai default 100)
        TaskInstance::where('kpi_period_id', $period->id)
            ->where('status', 'submitted')
            ->update([
                'status' => 'completed',
                'nilai' => 100,
                'updated_at' => now(),
            ]);

        // 4. Tugas approved/on_progress → MIA
        TaskInstance::where('kpi_period_id', $period->id)
            ->whereIn('status', ['approved', 'on_progress'])
            ->update([
                'status' => 'MIA',
                'nilai' => 0,
                'faktor_waktu' => 0,
                'skor_akhir' => 0,
                'updated_at' => now(),
            ]);
    }
}
