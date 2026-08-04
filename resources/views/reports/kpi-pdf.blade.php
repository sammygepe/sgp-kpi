<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan KPI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #2563eb;
            color: white;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }

        .rank-1 {
            background-color: #fef3c7;
        }

        .rank-2 {
            background-color: #f3f4f6;
        }

        .rank-3 {
            background-color: #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">📊 Laporan KPI</div>
        <div class="subtitle">
            Periode: {{ ucfirst($period) }} -
            {{ $period === 'year' ? $year : $month . '/' . $year }}
        </div>
        <div class="subtitle">Dicetak: {{ $generated_at }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Staf</th>
                <th>Atasan</th>
                <th>Rata-rata Skor</th>
                <th>Total Tugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr class="{{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : '')) }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['staff_name'] }}</td>
                <td>{{ $item['supervisor'] }}</td>
                <td><strong>{{ $item['avg_score'] }}</strong></td>
                <td>{{ $item['total_tasks'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Laporan ini dibuat secara otomatis oleh SGP KPI System
    </div>
</body>

</html>