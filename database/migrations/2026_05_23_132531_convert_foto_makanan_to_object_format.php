<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert foto_makanan from ["path1", "path2"] to [{"path":"path1","label":""},{"path":"path2","label":""}]
     */
    public function up(): void
    {
        $rows = DB::table('menu_makanans')->whereNotNull('foto_makanan')->get();

        foreach ($rows as $row) {
            $fotos = json_decode($row->foto_makanan, true);
            if (!is_array($fotos)) continue;

            $needsConversion = false;
            foreach ($fotos as $foto) {
                if (is_string($foto)) {
                    $needsConversion = true;
                    break;
                }
            }

            if ($needsConversion) {
                $converted = array_map(function ($item) {
                    if (is_string($item)) {
                        return ['path' => $item, 'label' => ''];
                    }
                    return $item;
                }, $fotos);

                DB::table('menu_makanans')
                    ->where('id', $row->id)
                    ->update(['foto_makanan' => json_encode($converted)]);
            }
        }
    }

    /**
     * Revert back to plain string array format
     */
    public function down(): void
    {
        $rows = DB::table('menu_makanans')->whereNotNull('foto_makanan')->get();

        foreach ($rows as $row) {
            $fotos = json_decode($row->foto_makanan, true);
            if (!is_array($fotos)) continue;

            $reverted = array_map(function ($item) {
                if (is_array($item) && isset($item['path'])) {
                    return $item['path'];
                }
                return $item;
            }, $fotos);

            DB::table('menu_makanans')
                ->where('id', $row->id)
                ->update(['foto_makanan' => json_encode($reverted)]);
        }
    }
};
