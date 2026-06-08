<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenFoodFactsService
{
    protected string $baseUrl = 'https://world.openfoodfacts.org';

    public function getByBarcode(string $barcode): ?array
    {
        $response = Http::get("{$this->baseUrl}/api/v2/product/{$barcode}.json");

        if ($response->failed() || $response->json('status') === 0) {
            return null;
        }

        return $this->normalise($response->json('product'));
    }

    public function search(string $query, int $limit = 1): array
    {
        $response = Http::get("{$this->baseUrl}/cgi/search.pl", [
            'search_terms' => $query,
            'search_simple' => 1,
            'action'       => 'process',
            'json'         => 1,
            'page_size'    => $limit,
            'fields'       => 'code,product_name,brands,nutriments,image_front_thumb_url',
        ]);

        if ($response->failed()) {
            return [];
        }

        return array_map(
            fn($p) => $this->normalise($p),
            $response->json('products') ?? []
        );
    }

    protected function normalise(array $product): array
    {
        $n = $product['nutriments'] ?? [];

        return [
            'barcode'  => $product['code'] ?? null,
            'name'     => $product['product_name'] ?? 'Unknown',
            'brand'    => $product['brands'] ?? null,
            'image'    => $product['image_front_thumb_url'] ?? null,
            'per_100g' => [
                'calories' => $n['energy-kcal_100g'] ?? null,
                'protein'  => $n['proteins_100g'] ?? null,
                'carbs'    => $n['carbohydrates_100g'] ?? null,
                'fat'      => $n['fat_100g'] ?? null,
                'fibre'    => $n['fiber_100g'] ?? null,
                'sugar'    => $n['sugars_100g'] ?? null,
                'salt'     => $n['salt_100g'] ?? null,
            ],
        ];
    }
}