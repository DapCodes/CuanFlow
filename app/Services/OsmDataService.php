<?php

namespace App\Services;

use App\Models\BusinessPoint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OsmDataService
{
    /**
     * Overpass API endpoint (public, free)
     */
    private string $overpassUrl = 'https://overpass-api.de/api/interpreter';

    /**
     * Predefined bounding boxes for Indonesian cities
     * Format: [south, west, north, east]
     */
    private array $areas = [
        'jakarta' => [-6.3700, 106.6850, -6.0800, 106.9730],
        'surabaya' => [-7.3700, 112.6000, -7.2000, 112.8500],
        'bandung' => [-7.1000, 107.4000, -6.7000, 107.8000],
        'cikarang' => [-6.4500, 107.0500, -6.1500, 107.3000],
        'bali' => [-8.8500, 115.0500, -8.0500, 115.7500],
        'yogyakarta' => [-7.8500, 110.3200, -7.7300, 110.4500],
        'semarang' => [-7.0700, 110.3500, -6.9400, 110.4800],
        'medan' => [3.5000, 98.5800, 3.7000, 98.7500],
        'makassar' => [-5.1900, 119.3700, -5.0800, 119.5000],
    ];

    /**
     * Get the bounding box for a given area name
     */
    public function getBoundingBox(string $area): array
    {
        return $this->areas[strtolower($area)] ?? $this->areas['jakarta'];
    }

    /**
     * Get list of available area names
     */
    public function getAvailableAreas(): array
    {
        return array_keys($this->areas);
    }

    /**
     * Fetch OSM data for a given area and store in database
     */
    public function fetchAndStore(string $area = 'jakarta'): array
    {
        $bbox = $this->getBoundingBox($area);

        Log::info("OSM Data Fetch: Starting for area '{$area}'", ['bbox' => $bbox]);

        $elements = $this->queryOverpassApi($bbox);

        if (empty($elements)) {
            Log::warning("OSM Data Fetch: No elements returned for area '{$area}'");
            return ['fetched' => 0, 'stored' => 0];
        }

        Log::info("OSM Data Fetch: Got " . count($elements) . " elements from Overpass API");

        $stored = $this->storeElements($elements);

        Log::info("OSM Data Fetch: Stored {$stored} business points");

        return ['fetched' => count($elements), 'stored' => $stored];
    }

    /**
     * Fetch OSM data for a custom bounding box
     */
    public function fetchAndStoreCustom(float $south, float $west, float $north, float $east): array
    {
        $bbox = [$south, $west, $north, $east];

        Log::info("OSM Data Fetch: Starting for custom bbox", ['bbox' => $bbox]);

        $elements = $this->queryOverpassApi($bbox);

        if (empty($elements)) {
            return ['fetched' => 0, 'stored' => 0];
        }

        $stored = $this->storeElements($elements);

        return ['fetched' => count($elements), 'stored' => $stored];
    }

    /**
     * Query the Overpass API for business-related nodes
     */
    private function queryOverpassApi(array $bbox): array
    {
        [$south, $west, $north, $east] = $bbox;
        $bboxStr = "{$south},{$west},{$north},{$east}";

        // Build Overpass QL query for business-related nodes
        $query = <<<OVERPASS
[out:json][timeout:180];
(
  node["shop"]({$bboxStr});
  node["amenity"~"restaurant|cafe|fast_food|marketplace|bank|pharmacy|fuel|cinema|theatre|bar|pub|nightclub|food_court|biergarten|ice_cream|bbq"](${bboxStr});
  node["office"]({$bboxStr});
  node["tourism"~"hotel|hostel|motel|guest_house|attraction|museum|gallery|zoo|theme_park|viewpoint|information"](${bboxStr});
);
out body;
OVERPASS;

        try {
            $response = Http::withoutVerifying()
                ->timeout(200)
                ->retry(3, 5000)
                ->asForm()
                ->post($this->overpassUrl, [
                    'data' => $query,
                ]);

            if (!$response->successful()) {
                Log::error("Overpass API error", [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return [];
            }

            $data = $response->json();

            return $data['elements'] ?? [];

        } catch (\Exception $e) {
            Log::error("Overpass API exception: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Store OSM elements as BusinessPoint records (chunked)
     */
    private function storeElements(array $elements): int
    {
        $stored = 0;

        // Process in chunks of 500
        $chunks = array_chunk($elements, 500);

        foreach ($chunks as $chunk) {
            $records = [];

            foreach ($chunk as $element) {
                if (!isset($element['lat'], $element['lon'])) {
                    continue;
                }

                $tags = $element['tags'] ?? [];
                $category = $this->extractCategory($tags);

                if (!$category) {
                    continue;
                }

                $records[] = [
                    'name' => $this->extractName($tags),
                    'category' => $category['main'],
                    'sub_category' => $category['sub'],
                    'latitude' => round($element['lat'], 7),
                    'longitude' => round($element['lon'], 7),
                    'raw_tags' => json_encode($tags),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($records)) {
                // Use upsert to avoid duplicates based on lat/lng
                BusinessPoint::insert($records);
                $stored += count($records);
            }
        }

        return $stored;
    }

    /**
     * Extract the main category and sub-category from OSM tags
     */
    private function extractCategory(array $tags): ?array
    {
        // Priority order for category extraction
        if (isset($tags['shop'])) {
            return ['main' => 'shop', 'sub' => $tags['shop']];
        }

        if (isset($tags['amenity'])) {
            $amenity = $tags['amenity'];
            // Only include business-relevant amenities
            $validAmenities = [
                'restaurant', 'cafe', 'fast_food', 'marketplace',
                'bank', 'pharmacy', 'fuel', 'cinema', 'theatre',
                'bar', 'pub', 'nightclub', 'food_court',
                'biergarten', 'ice_cream', 'bbq',
            ];

            if (in_array($amenity, $validAmenities)) {
                return ['main' => 'amenity', 'sub' => $amenity];
            }

            return null;
        }

        if (isset($tags['office'])) {
            return ['main' => 'office', 'sub' => $tags['office']];
        }

        if (isset($tags['tourism'])) {
            return ['main' => 'tourism', 'sub' => $tags['tourism']];
        }

        return null;
    }

    /**
     * Extract a human-readable name from OSM tags
     */
    private function extractName(array $tags): ?string
    {
        return $tags['name'] ?? $tags['name:en'] ?? $tags['name:id'] ?? null;
    }

    /**
     * Clear all business points (useful before re-fetching)
     */
    public function clearAll(): int
    {
        return BusinessPoint::truncate() ? BusinessPoint::count() : 0;
    }

    /**
     * Clear business points within a bounding box
     */
    public function clearArea(string $area): int
    {
        $bbox = $this->getBoundingBox($area);

        return BusinessPoint::whereBetween('latitude', [$bbox[0], $bbox[2]])
            ->whereBetween('longitude', [$bbox[1], $bbox[3]])
            ->delete();
    }
}
