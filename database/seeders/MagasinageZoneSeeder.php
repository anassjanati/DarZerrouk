<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\SousZone;
use App\Models\SousSousZone;

class MagasinageZoneSeeder extends Seeder
{
    /**
     * Crée 15 zones de Magasinage avec hiérarchie (zones + sous-zones + sous-sous-zones)
     * Codes: D1..D15, puis D1/1..D1/3, et D1/1/1..D1/1/3 (idempotent, sans doublons).
     */
    public function run(): void
    {
        // Liste des 15 zones de Magasinage à créer (préfixe "D")
        $topZoneCodes = [];
        for ($i = 1; $i <= 15; $i++) {
            $topZoneCodes[] = 'D' . $i;
        }

        foreach ($topZoneCodes as $code) {
            // 1) Zone de niveau 1 (magasinage)
            $zone = Zone::where('code', $code)->first();

            if (!$zone) {
                $zone = Zone::create([
                    'name'        => "Magasinage {$code}",
                    'code'        => $code,
                    'type'        => 'magasinage',
                    'description' => "Zone de réserve {$code}",
                    'is_active'   => true,
                ]);
            } else {
                // Si une zone existe déjà avec ce code mais n'est pas "magasinage",
                // on ne modifie pas son type et on passe à la suivante pour éviter les collisions.
                if ($zone->type !== 'magasinage') {
                    // Passer à la suivante pour éviter de créer des sous-zones sous une mauvaise zone.
                    continue;
                }
            }

            // 2) Créer 3 sous-zones: D#/1, D#/2, D#/3
            for ($i = 1; $i <= 3; $i++) {
                $sousCode = "{$code}/{$i}";

                $sous = SousZone::firstOrCreate(
                    ['zone_id' => $zone->id, 'code' => $sousCode],
                    ['name' => $sousCode, 'is_active' => true]
                );

                // 3) Créer 3 sous-sous-zones: D#/i/1, D#/i/2, D#/i/3
                for ($j = 1; $j <= 3; $j++) {
                    $ssCode = "{$sousCode}/{$j}";

                    SousSousZone::firstOrCreate(
                        ['sous_zone_id' => $sous->id, 'code' => $ssCode],
                        ['name' => $ssCode, 'is_active' => true]
                    );
                }
            }
        }
    }
}
