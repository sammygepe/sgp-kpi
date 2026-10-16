<?php

use Illuminate\Support\Facades\Schedule;

// Auto-close periode KPI (jalan setiap hari jam 00:05)
Schedule::command('kpi:auto-close')->dailyAt('00:05');