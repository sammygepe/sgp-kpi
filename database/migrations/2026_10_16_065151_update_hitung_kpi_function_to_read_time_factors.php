<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sql = "
            CREATE OR REPLACE FUNCTION hitung_kpi(
                p_bobot INT,
                p_nilai INT,
                p_deadline DATE,
                p_tanggal_submit DATE
            )
            RETURNS DECIMAL(10,2) AS \$\$
            DECLARE
                v_selisih INT;
                v_faktor DECIMAL(3,2);
                v_skor DECIMAL(10,2);
            BEGIN
                -- Hitung selisih hari
                v_selisih := (p_tanggal_submit - p_deadline);
                
                -- Ambil faktor dari tabel time_factors
                SELECT faktor INTO v_faktor
                FROM time_factors
                WHERE v_selisih >= selisih_min 
                  AND v_selisih <= selisih_max
                  AND is_active = true
                ORDER BY selisih_min DESC
                LIMIT 1;
                
                -- Kalau tabel kosong atau tidak ada match, default 1.0
                IF v_faktor IS NULL THEN
                    v_faktor := 1.0;
                END IF;
                
                -- Hitung skor akhir
                v_skor := p_bobot * p_nilai * v_faktor;
                
                RETURN v_skor;
            END;
            \$\$ LANGUAGE plpgsql;
        ";

        DB::unprepared($sql);
    }

    public function down(): void
    {
        // Kembalikan ke versi hardcode (opsional)
        $sql = "
            CREATE OR REPLACE FUNCTION hitung_kpi(
                p_bobot INT,
                p_nilai INT,
                p_deadline DATE,
                p_tanggal_submit DATE
            )
            RETURNS DECIMAL(10,2) AS \$\$
            DECLARE
                v_selisih INT;
                v_faktor DECIMAL(3,2);
                v_skor DECIMAL(10,2);
            BEGIN
                v_selisih := (p_tanggal_submit - p_deadline);
                
                CASE
                    WHEN v_selisih <= -14 THEN v_faktor := 1.5;
                    WHEN v_selisih <= -7 THEN v_faktor := 1.4;
                    WHEN v_selisih <= -3 THEN v_faktor := 1.3;
                    WHEN v_selisih <= -2 THEN v_faktor := 1.2;
                    WHEN v_selisih <= -1 THEN v_faktor := 1.1;
                    WHEN v_selisih <= 0 THEN v_faktor := 1.0;
                    WHEN v_selisih = 1 THEN v_faktor := 0.9;
                    WHEN v_selisih = 2 THEN v_faktor := 0.8;
                    WHEN v_selisih = 3 THEN v_faktor := 0.7;
                    WHEN v_selisih <= 7 THEN v_faktor := 0.6;
                    WHEN v_selisih <= 14 THEN v_faktor := 0.5;
                    WHEN v_selisih <= 21 THEN v_faktor := 0.4;
                    WHEN v_selisih <= 30 THEN v_faktor := 0.3;
                    WHEN v_selisih <= 60 THEN v_faktor := 0.2;
                    WHEN v_selisih <= 90 THEN v_faktor := 0.1;
                    ELSE v_faktor := 0;
                END CASE;
                
                v_skor := p_bobot * p_nilai * v_faktor;
                RETURN v_skor;
            END;
            \$\$ LANGUAGE plpgsql;
        ";

        DB::unprepared($sql);
    }
};
