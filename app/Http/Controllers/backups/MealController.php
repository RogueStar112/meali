<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenFoodFacts;
use App\Services\OpenFoodFactsService;

class MealController extends Controller
{
    public function index()
    {
        // Group meals by date descending (newest day on left), meals within a day ascending by creation
        $mealsByDate = Meal::orderBy('eaten_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(fn($meal) => $meal->eaten_at->format('Y-m-d'))
            ->reverse();

        return view('meals.index', compact('mealsByDate'));
    }

    public function api_search(Request $request, $query) {

        $query = OpenFoodFacts::find($query);

        return $query->message[0];

    }

    public function api_search_v2(Request $request, OpenFoodFactsService $off, $query)
    {

        $response = Http::get('https://world.openfoodfacts.org/cgi/search.pl', [
            'search_terms' => $query,
            'search_simple' => 1,
            'action'        => 'process',
            'json'          => 1,
            'page_size'     => 1,
            'fields'        => 'product_name,brands,nutriments',
        ]);

        $product = $response->json('products.0');

        if (!$product) {
            return response()->json(['error' => 'No product found'], 404);
        }

        $n = $product['nutriments'] ?? [];

            return response()->json([
        'name'     => $product['product_name'] ?? 'Unknown',
        'calories' => $n['energy-kcal_100g'] ?? null,
        'protein'  => $n['proteins_100g'] ?? null,
        'carbs'    => $n['carbohydrates_100g'] ?? null,
        'fat'      => $n['fat_100g'] ?? null,
         ]);
    }

    public function barcode_test() {
        $product = OpenFoodFacts::barcode('7622210898548');
        dd($product);

        /*
            array:248 [▼ // app/Http/Controllers/MealController.php:25
                "_id" => "20203467"
                "_keywords" => array:17 [▶]
                "added_countries_tags" => []
                "additives_n" => 4
                "additives_original_tags" => array:4 [▶]
                "additives_tags" => array:4 [▶]
                "allergens" => "Eier, Gluten, Milch, Schalenfrüchte"
                "allergens_from_ingredients" => "en:milk, en:milk, en:eggs, en:nuts, en:gluten, Weizenmehl, Hühnereier*, Butter, Magermilchpulver"
                "allergens_from_user" => "(en) "
                "allergens_hierarchy" => array:4 [▶]
                "allergens_lc" => "de"
                "allergens_tags" => array:4 [▶]
                "amino_acids_prev_tags" => []
                "amino_acids_tags" => []
                "brands" => "Deluxe, Asolo Dolce, Lidl"
                "brands_tags" => array:3 [▶]
                "categories" => "Cantucci"
                "categories_hierarchy" => array:7 [▶]
                "categories_lc" => "de"
                "categories_properties" => array:3 [▶]
                "categories_properties_tags" => array:10 [▶]
                "categories_tags" => array:7 [▶]
                "category_properties" => []
                "checkers_tags" => []
                "ciqual_food_name_tags" => array:1 [▶]
                "cities_tags" => []
                "code" => "20203467"
                "codes_tags" => array:6 [▶]
                "compared_to_category" => "en:cantucci"
                "complete" => 0
                "completeness" => 0.6875
                "correctors_tags" => array:6 [▶]
                "countries" => "Deutschland, Schweiz"
                "countries_hierarchy" => array:2 [▶]
                "countries_lc" => "de"
                "countries_tags" => array:2 [▶]
                "created_t" => 1542894298
                "creator" => "openfoodfacts-contributors"
                "data_quality_bugs_tags" => []
                "data_quality_completeness_tags" => array:18 [▶]
                "data_quality_dimensions" => array:2 [▶]
                "data_quality_errors_tags" => []
                "data_quality_info_tags" => array:5 [▶]
                "data_quality_tags" => array:26 [▶]
                "data_quality_warnings_tags" => array:3 [▶]
                "data_sources" => "App - yuka, Apps, App - Speisekammer, App - smoothie-openfoodfacts"
                "data_sources_tags" => array:4 [▶]
                "debug_param_sorted_langs" => array:3 [▶]
                "ecoscore_data" => array:11 [▶]
                "ecoscore_grade" => "d"
                "ecoscore_score" => 37
                "ecoscore_tags" => array:1 [▶]
                "editors_tags" => array:9 [▶]
                "emb_codes" => ""
                "emb_codes_hierarchy" => []
                "emb_codes_lc" => "de"
                "emb_codes_tags" => []
                "entry_dates_tags" => array:3 [▶]
                "expiration_date" => ""
                "food_groups" => "en:biscuits-and-cakes"
                "food_groups_tags" => array:2 [▶]
                "forest_footprint_data" => array:3 [▶]
                "generic_name" => ""
                "generic_name_de" => ""
                "generic_name_en" => ""
                "generic_name_fr" => ""
                "id" => "20203467"
                "image_front_small_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/front_de.19.200.jpg"
                "image_front_thumb_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/front_de.19.100.jpg"
                "image_front_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/front_de.19.400.jpg"
                "image_ingredients_small_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/ingredients_en.16.200.jpg"
                "image_ingredients_thumb_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/ingredients_en.16.100.jpg"
                "image_ingredients_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/ingredients_en.16.400.jpg"
                "image_nutrition_small_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/nutrition_de.21.200.jpg"
                "image_nutrition_thumb_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/nutrition_de.21.100.jpg"
                "image_nutrition_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/nutrition_de.21.400.jpg"
                "image_small_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/front_de.19.200.jpg"
                "image_thumb_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/front_de.19.100.jpg"
                "image_url" => "https://images.openfoodfacts.org/images/products/000/002/020/3467/front_de.19.400.jpg"
                "images" => array:12 [▶]
                "informers_tags" => array:8 [▶]
                "ingredients" => array:22 [▶]
                "ingredients_analysis" => array:3 [▶]
                "ingredients_analysis_tags" => array:3 [▶]
                "ingredients_debug" => []
                "ingredients_from_palm_oil_tags" => []
                "ingredients_hierarchy" => array:37 [▶]
                "ingredients_ids_debug" => []
                "ingredients_lc" => "de"
                "ingredients_n" => 22
                "ingredients_n_tags" => array:2 [▶]
                "ingredients_non_nutritive_sweeteners_n" => 0
                "ingredients_original_tags" => array:22 [▶]
                "ingredients_percent_analysis" => 1
                "ingredients_sweeteners_n" => 0
                "ingredients_tags" => array:37 [▶]
                "ingredients_text" => "Weizenmehl, Zucker, 20% Haselnüsse, Hühnereier*, pflanzliche Margarine (Palmfett, Wasser, Sonnenblumenöl, Emulgator: Mono - und Diglyceride von Speisefettsäuren ▶"
                "ingredients_text_de" => "Weizenmehl, Zucker, 20% Haselnüsse, Hühnereier*, pflanzliche Margarine (Palmfett, Wasser, Sonnenblumenöl, Emulgator: Mono - und Diglyceride von Speisefettsäuren ▶"
                "ingredients_text_en" => ""
                "ingredients_text_fr" => ""
                "ingredients_text_with_allergens" => "<span class="allergen">Weizenmehl</span>, Zucker, 20% Haselnüsse, <span class="allergen">Hühnereier*</span>, pflanzliche Margarine (Palmfett, Wasser, Sonnenblum ▶"
                "ingredients_text_with_allergens_de" => "<span class="allergen">Weizenmehl</span>, Zucker, 20% Haselnüsse, <span class="allergen">Hühnereier*</span>, pflanzliche Margarine (Palmfett, Wasser, Sonnenblum ▶"
                "ingredients_text_with_allergens_en" => ""
                "ingredients_text_with_allergens_fr" => ""
                "ingredients_that_may_be_from_palm_oil_tags" => []
                "ingredients_with_specified_percent_n" => 1
                "ingredients_with_specified_percent_sum" => 20
                "ingredients_with_unspecified_percent_n" => 17
                "ingredients_with_unspecified_percent_sum" => 80
                "ingredients_without_ciqual_codes" => array:9 [▶]
                "ingredients_without_ciqual_codes_n" => 9
                "ingredients_without_ecobalyse_ids" => array:11 [▶]
                "ingredients_without_ecobalyse_ids_n" => 11
                "interface_version_created" => "20120622"
                "interface_version_modified" => "20150316.jqm2"
                "known_ingredients_n" => 22
                "labels" => ""
                "labels_hierarchy" => []
                "labels_lc" => "de"
                "labels_tags" => []
                "lang" => "de"
                "languages" => array:3 [▶]
                "languages_codes" => array:3 [▶]
                "languages_hierarchy" => array:3 [▶]
                "languages_tags" => array:5 [▶]
                "last_edit_dates_tags" => array:3 [▶]
                "last_editor" => "new-nutrition-bot"
                "last_image_dates_tags" => array:3 [▶]
                "last_image_t" => 1762195659
                "last_modified_by" => "new-nutrition-bot"
                "last_modified_t" => 1774470643
                "last_updated_t" => 1774470643
                "lc" => "de"
                "link" => ""
                "main_countries_tags" => []
                "manufacturing_places" => ""
                "manufacturing_places_hierarchy" => []
                "manufacturing_places_lc" => "de"
                "manufacturing_places_tags" => []
                "max_imgid" => 6
                "minerals_prev_tags" => []
                "minerals_tags" => []
                "misc_tags" => array:24 [▶]
                "nova_group" => 4
                "nova_group_debug" => ""
                "nova_groups" => "4"
                "nova_groups_markers" => array:2 [▶]
                "nova_groups_tags" => array:1 [▶]
                "nucleotides_prev_tags" => []
                "nucleotides_tags" => []
                "nutrient_levels" => array:4 [▶]
                "nutrient_levels_tags" => array:4 [▶]
                "nutriments" => array:58 [▶]
                "nutriments_estimated" => array:34 [▶]
                "nutriscore" => array:2 [▶]
                "nutriscore_2021_tags" => array:1 [▶]
                "nutriscore_2023_tags" => array:1 [▶]
                "nutriscore_data" => array:15 [▶]
                "nutriscore_grade" => "d"
                "nutriscore_score" => 18
                "nutriscore_score_opposite" => -18
                "nutriscore_tags" => array:1 [▶]
                "nutriscore_version" => "2023"
                "nutrition_data" => "on"
                "nutrition_data_per" => "100g"
                "nutrition_data_prepared_per" => "100g"
                "nutrition_grade_fr" => "d"
                "nutrition_grades" => "d"
                "nutrition_grades_tags" => array:1 [▶]
                "nutrition_score_beverage" => 0
                "nutrition_score_debug" => ""
                "nutrition_score_warning_fruits_vegetables_legumes_estimate_from_ingredients" => 1
                "nutrition_score_warning_fruits_vegetables_legumes_estimate_from_ingredients_value" => 0
                "nutrition_score_warning_fruits_vegetables_nuts_estimate_from_ingredients" => 1
                "nutrition_score_warning_fruits_vegetables_nuts_estimate_from_ingredients_value" => 20
                "obsolete" => ""
                "obsolete_since_date" => ""
                "origin" => ""
                "origin_de" => ""
                "origin_en" => ""
                "origin_fr" => ""
                "origins" => ""
                "origins_hierarchy" => []
                "origins_lc" => "de"
                "origins_tags" => []
                "other_nutritional_substances_tags" => []
                "packaging" => ""
                "packaging_hierarchy" => []
                "packaging_lc" => "fr"
                "packaging_materials_tags" => []
                "packaging_old" => ""
                "packaging_recycling_tags" => []
                "packaging_shapes_tags" => []
                "packaging_tags" => []
                "packaging_text" => ""
                "packaging_text_de" => ""
                "packaging_text_en" => ""
                "packaging_text_fr" => ""
                "packagings" => []
                "packagings_complete" => 0
                "packagings_materials" => []
                "photographers_tags" => array:4 [▶]
                "pnns_groups_1" => "Sugary snacks"
                "pnns_groups_1_tags" => array:2 [▶]
                "pnns_groups_2" => "Biscuits and cakes"
                "pnns_groups_2_tags" => array:2 [▶]
                "popularity_key" => 24950000022
                "popularity_tags" => array:92 [▶]
                "product_name" => "Lidl Deluxe Cantuccini mit Haselnüssen 20203467 Kekse mit Haselnüssen"
                "product_name_de" => "Lidl Deluxe Cantuccini mit Haselnüssen 20203467 Kekse mit Haselnüssen"
                "product_name_en" => ""
                "product_name_fr" => "Cantuccini with hazelnuts"
                "product_quantity" => 200
                "product_quantity_unit" => "g"
                "product_type" => "food"
                "purchase_places" => ""
                "purchase_places_hierarchy" => []
                "purchase_places_lc" => "de"
                "purchase_places_tags" => []
                "quantity" => "200g"
                "removed_countries_tags" => []
                "rev" => 24
                "scans_n" => 14
                "schema_version" => 996
                "selected_images" => array:3 [▶]
                "sortkey" => 1594928721
                "states" => "Auszufüllen, Nährwertinformationen ausgefüllt, Zutaten ausgefüllt, Verbrauchsdatum auszufüllen, Verpackungscode auszufüllen, Merkmale auszufüllen, Herkunft ausz ▶"
                "states_hierarchy" => array:18 [▶]
                "states_tags" => array:18 [▶]
                "stores" => "Lidl"
                "stores_hierarchy" => array:1 [▶]
                "stores_lc" => "de"
                "stores_tags" => array:1 [▶]
                "teams" => "pain-au-chocolat,chocolatine,la-robe-est-bleue"
                "teams_tags" => array:3 [▶]
                "traces" => "Senf, Schalenfrüchte, Soja"
                "traces_from_ingredients" => "Senf, Soja, Schalenfrüchten"
                "traces_from_user" => "(en) "
                "traces_hierarchy" => array:3 [▶]
                "traces_lc" => "de"
                "traces_tags" => array:3 [▶]
                "unique_scans_n" => 14
                "unknown_ingredients_n" => 0
                "unknown_nutrients_tags" => []
                "update_key" => "new-nutrition"
                "vitamins_prev_tags" => []
                "vitamins_tags" => []
                "weighers_tags" => []
                ]
        */
    }

    public function create()
    {

        $previous_meals = Meal::latest()->take(24)->get();

        return view('meals.create', [
            'today' => now()->format('Y-m-d'),
            'previous_meals' => $previous_meals,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'eaten_at' => 'required|date',
            'calories' => 'required|integer|min:0|max:9999',
            'protein'  => 'required|numeric|min:0|max:999',
            'carbs'    => 'required|numeric|min:0|max:999',
            'fat'      => 'required|numeric|min:0|max:999',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

         $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('meals', 'public');
        } elseif ($request->filled('reused_image_path')) {
            $imagePath = $request->reused_image_path; // reuse existing path
        }

        Meal::create(array_merge($validated, ['image_path' => $imagePath]));

        return redirect()->route('meals.index')->with('success', 'Meal logged!');
    }

    public function edit($id)
    {   

        $meal = Meal::find($id);

        return view('meals.edit', [
            'today' => now()->format('Y-m-d'),
            'meal' => $meal,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'eaten_at' => 'required|date_format:Y-m-d H:i:s',
            'calories' => 'required|integer|min:0|max:9999',
            'protein'  => 'required|numeric|min:0|max:999',
            'carbs'    => 'required|numeric|min:0|max:999',
            'fat'      => 'required|numeric|min:0|max:999',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('meals', 'public');
        }

        $meal = Meal::find($id);

        $meal->update(array_merge($validated, ['image_path' => $imagePath]));

        $meal_name = $meal->name;

        return redirect()->route('meals.index')->with('success', "Meal $meal_name ($id) updated!");
    }

    public function destroy(Meal $meal)
    {
        if ($meal->image_path) {
            Storage::disk('public')->delete($meal->image_path);
        }

        $meal->delete();

        return redirect()->route('meals.index')->with('success', 'Meal removed.');
    }

     public function timeline()
    {
        // Return a clean, timezone-safe array for Alpine.js consumption
        $meals = Meal::orderBy('eaten_at')
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'        => $m->id,
                'name'      => $m->name,
                'eaten_at'  => $m->eaten_at->format('Y-m-d'),
                'eaten_at_time' => $m->eaten_at->format('H:i'),
                'eaten_at_hour' => $m->eaten_at->format('H'),
                'calories'  => (int) $m->calories,
                'protein'   => (float) $m->protein,
                'carbs'     => (float) $m->carbs,
                'fat'       => (float) $m->fat,
                'image_url' => $m->image_path ? Storage::url($m->image_path) : null,
            ]);

        return view('meals.timeline', compact('meals'));
    }
}