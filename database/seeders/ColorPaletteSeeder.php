<?php

namespace Database\Seeders;

use App\Models\ColorPalette;
use Illuminate\Database\Seeder;

class ColorPaletteSeeder extends Seeder
{
    public function run(): void
    {
        $palettes = [
            // 1 — Default: CuanFlow Classic (original)
            [
                'name' => 'CuanFlow Classic',
                'slug' => 'cuanflow-classic',
                'color_yellow' => '#F0E491',
                'color_olive' => '#BBC863',
                'color_green' => '#658C58',
                'color_dark' => '#31694E',
                'is_default' => true,
                'sort_order' => 1,
            ],
            // 2 — Ocean Breeze: calm coastal blues & teals
            [
                'name' => 'Ocean Breeze',
                'slug' => 'ocean-breeze',
                'color_yellow' => '#B3E5FC',
                'color_olive' => '#4FC3F7',
                'color_green' => '#0288D1',
                'color_dark' => '#01579B',
                'is_default' => false,
                'sort_order' => 2,
            ],
            // 3 — Sunset Harvest: warm amber & rich brown
            [
                'name' => 'Sunset Harvest',
                'slug' => 'sunset-harvest',
                'color_yellow' => '#FFE0B2',
                'color_olive' => '#FFB74D',
                'color_green' => '#E65100',
                'color_dark' => '#8D3200',
                'is_default' => false,
                'sort_order' => 3,
            ],
            // 4 — Royal Plum: luxurious purple tones
            [
                'name' => 'Royal Plum',
                'slug' => 'royal-plum',
                'color_yellow' => '#EDE7F6',
                'color_olive' => '#CE93D8',
                'color_green' => '#7B1FA2',
                'color_dark' => '#4A0072',
                'is_default' => false,
                'sort_order' => 4,
            ],
            // 5 — Rose Gold: feminine blush & dusty rose
            [
                'name' => 'Rose Gold',
                'slug' => 'rose-gold',
                'color_yellow' => '#FCE4EC',
                'color_olive' => '#F48FB1',
                'color_green' => '#C2185B',
                'color_dark' => '#880E4F',
                'is_default' => false,
                'sort_order' => 5,
            ],
            // 6 — Midnight Slate: sophisticated dark navy
            [
                'name' => 'Midnight Slate',
                'slug' => 'midnight-slate',
                'color_yellow' => '#B0BEC5',
                'color_olive' => '#607D8B',
                'color_green' => '#37474F',
                'color_dark' => '#102027',
                'is_default' => false,
                'sort_order' => 6,
            ],
            // 7 — Tropical Mint: fresh emerald & lime
            [
                'name' => 'Tropical Mint',
                'slug' => 'tropical-mint',
                'color_yellow' => '#CCFF90',
                'color_olive' => '#69F0AE',
                'color_green' => '#00C853',
                'color_dark' => '#1B5E20',
                'is_default' => false,
                'sort_order' => 7,
            ],
            // 8 — Desert Sand: earthy sand & terracotta
            [
                'name' => 'Desert Sand',
                'slug' => 'desert-sand',
                'color_yellow' => '#FFF8E1',
                'color_olive' => '#FFCC80',
                'color_green' => '#A1750A',
                'color_dark' => '#5D4037',
                'is_default' => false,
                'sort_order' => 8,
            ],
            // 9 — Arctic Aurora: icy cyan & aquamarine
            [
                'name' => 'Arctic Aurora',
                'slug' => 'arctic-aurora',
                'color_yellow' => '#E0F7FA',
                'color_olive' => '#80DEEA',
                'color_green' => '#00ACC1',
                'color_dark' => '#006064',
                'is_default' => false,
                'sort_order' => 9,
            ],
            // 10 — Cherry Blossom: sakura pink & deep red
            [
                'name' => 'Cherry Blossom',
                'slug' => 'cherry-blossom',
                'color_yellow' => '#FFDDE3',
                'color_olive' => '#FF8A80',
                'color_green' => '#E53935',
                'color_dark' => '#7F0000',
                'is_default' => false,
                'sort_order' => 10,
            ],
            // 11 — Golden Hour: rich gold & vintage bronze
            [
                'name' => 'Golden Hour',
                'slug' => 'golden-hour',
                'color_yellow' => '#FFFDE7',
                'color_olive' => '#FFF176',
                'color_green' => '#F9A825',
                'color_dark' => '#7F6000',
                'is_default' => false,
                'sort_order' => 11,
            ],
            // 12 — Indigo Dreams: deep indigo & periwinkle
            [
                'name' => 'Indigo Dreams',
                'slug' => 'indigo-dreams',
                'color_yellow' => '#E8EAF6',
                'color_olive' => '#9FA8DA',
                'color_green' => '#3949AB',
                'color_dark' => '#1A237E',
                'is_default' => false,
                'sort_order' => 12,
            ],
            // 13 — Espresso: rich coffee browns
            [
                'name' => 'Espresso',
                'slug' => 'espresso',
                'color_yellow' => '#EFEBE9',
                'color_olive' => '#BCAAA4',
                'color_green' => '#6D4C41',
                'color_dark' => '#3E2723',
                'is_default' => false,
                'sort_order' => 13,
            ],
            // 14 — Neon Jungle: vibrant electric green on dark
            [
                'name' => 'Neon Jungle',
                'slug' => 'neon-jungle',
                'color_yellow' => '#F0FFF4',
                'color_olive' => '#86EFAC',
                'color_green' => '#16A34A',
                'color_dark' => '#052E16',
                'is_default' => false,
                'sort_order' => 14,
            ],
            // 15 — Monochrome Studio: clean grayscale elegance
            [
                'name' => 'Monochrome Studio',
                'slug' => 'monochrome-studio',
                'color_yellow' => '#F3F4F6',
                'color_olive' => '#9CA3AF',
                'color_green' => '#374151',
                'color_dark' => '#111827',
                'is_default' => false,
                'sort_order' => 15,
            ],
        ];

        foreach ($palettes as $palette) {
            ColorPalette::updateOrCreate(['slug' => $palette['slug']], $palette);
        }
    }
}
