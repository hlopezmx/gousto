<?php

/**
 * Created by PhpStorm.
 * User: Hugo Lopez Tovar
 * Date: 22/03/2016
 * Time: 9:23
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Recipe;


/**
 * Class RecipesController
 * @package App\Http\Controllers
 */
class RecipesController extends Controller
{


    /**
     * return the recipe info for a given ID, loading from CSV, and validating it.
     *
     * @param $id
     * @return bool
     */
    public static function loadRecipeById($id)
    {
        // make sure id is numeric
        $id = intval($id);

        $csvData = loadCSV();
        if (!$csvData) return false;
        $recipes = $csvData["recipes"];

        // does the id exist, and is it different than zero? (zero would return the csv headers!)
        if (($id <= 0) || (!isset($recipes[$id]))) {
            return false;
        }

        return $recipes[$id];
    }

    /**
     * Returns a collection of recipes by a given cuisine. Paginated
     *
     * @param $cuisine
     * @param $page
     * @return mixed
     */
    public static function loadRecipesByCuisine($cuisine, $page)
    {
        // a cuisine must be specified
        if (!$cuisine) return response()->json(['error' => 'A cuisine must be specified.']);

        // assuming a page size of two recipes
        $pageSize = 2;
        $csvData = loadCSV();
        if (!$csvData) return response()->json(['error' => 'Recipes are not accessible at the moment.']);
        $recipes = $csvData["recipes"];

        // we extract the recipes for this cuisine
        $cuisineRecipes = [];
        foreach ($recipes as $recipe) {
            if ($recipe['recipe_cuisine'] == $cuisine) {
                $cuisineRecipes[] = $recipe;
            }
        }

        // if no recipes for this cuisine, inform about it
        if (!$cuisineRecipes) return response()->json(['error' => 'No recipes have been found for ' . $cuisine . ' cuisine.']);

        // get a slice of the array to represent this page
        $pageRecipes = array_slice($cuisineRecipes, $pageSize * ($page - 1), $pageSize);

        if (($cuisineRecipes) && (!$pageRecipes))
            return response()->json(['error' => 'No more pages to show for ' . $cuisine . ' cuisine.']);
        else
            return $pageRecipes;
    }


    /**
     * Gets the recipe by ID, using an array with keys populated from the csv
     *
     * @param $id
     * @return recipe in JSON format
     */
    public function getRecipeById($id)
    {
        // return the recipe data as a Recipe object
        $recipe = self::loadRecipeById($id);
        if (!$recipe)
            return response()->json(['error' => 'Recipe not found.']);
        else
            return $recipe;

    }

    /**
     * Gets the recipe by ID, using an instance of the Recipe class
     *
     * @param $id
     * @return Recipe in JSON format
     */
    public function getRecipeObjectById($id)
    {
        // creates the Recipe object, populating is properties
        $recipe = self::loadRecipeById($id);
        if (!$recipe) return response()->json(['error' => 'Recipe not found.']);

        // return the recipe data in JSON format, using the implemented __toString() method
        return Recipe::populate($recipe);
    }


    /**
     * returns set of recipes by cuisine in JSON format
     *
     * @param Request $request
     * @param $cuisine
     * @return recipes in JSON format
     */
    public function getRecipesByCuisine(Request $request, $cuisine)
    {
        // get the current page, dismiss non-numeric values
        $page = (intval($request->input('page')) > 0 ? $request->input('page') : 1);

        // get the collection of recicpes for the given cuisine and page
        $recipes = self::loadRecipesByCuisine($cuisine, $page);

        // return the recipes in JSON format
        return $recipes;

    }


    /**
     * This method is just to capture when a cuisine was not given,
     * otherwise, the returned message 'recipe not found' would be confusing.
     *
     * @return JSON
     */
    public function getRecipesByCuisineNotDefined()
    {
        return response()->json(['error' => 'A cuisine must be specified.']);
    }


    /**
     * Rate a recipe, acceepting a number between 1 and 5
     *
     * @param $id
     * @param $rating
     * @return result message in JSON format
     */
    public function rateRecipe($id, $rating)
    {
        // get the recipe
        $recipe = self::loadRecipeById($id);
        if (!$recipe) return response()->json(['error' => 'Recipe not found.']);

        // validates rating number
        if ((!is_numeric($rating)) || ($rating < 1) || ($rating > 5)) {
            return response()->json(['error' => 'Rate must be a number between 1 and 5.']);
        }

        // add a property to the array
        $recipe["latest_rating"] = $rating;

        // stores the recipe's id and the rating to a ratings.csv file
        saveRatesCSV($id, $rating);

        // return the recipe data in JSON format
        return response()->json(['result' => 'Recipe ' . $id . ' has been rated as ' . $rating]);
    }


    /**
     * Updates a recipe.
     *   Only updates those inputs included in the request, except: id, created_at, updated_at
     *   The updated_at field is refreshed with the current date and time
     *
     * @param Request $request - the list of inputs to update
     * @param $id - the recipe id to be updated
     * @return updated recipe in JSON format
     */
    public function updateRecipe(Request $request, $id)
    {
        // our id is numeric
        $id = intval($id);

        // load the csv into memory
        $csvData = loadCSV();
        if (!$csvData) return response()->json(['error' => 'Recipes are not accessible at the moment.']);
        $header = $csvData["header"];
        $recipes = $csvData["recipes"];

        // does the id exist, and is it different than zero? (zero would return the csv headers!)
        if (($id <= 0) || (!isset($recipes[$id]))) return response()->json(['error' => 'Recipe not found.']);

        // we check which of our properties we have received and update only those
        foreach ($recipes[$id] as $key => $value) {

            // we don't want to uese the received id, created_at, and updated_at
            if (($header == 'id') || ($header == 'created_at') || ($header == 'updated_at')) continue;

            // have we receive this property?
            if ($request->has($key)) {
                // update it in the array
                $recipes[$id][$key] = $request->input($key);
            }

            // updates the updated_at field
            $recipes[$id]['updated_at'] = date("d/m/Y H:i:s", time());
        }

        // update the csv file
        saveCSV($header, $recipes, false);

        // we return the update recipe in JSON format
        return $recipes[$id];

    }


    public function addRecipe(Request $request)
    {

        // load the csv into memory
        $csvData = loadCSV();
        if (!$csvData) return response()->json(['error' => 'Recipes are not accessible at the moment.']);
        $headers = $csvData["header"];
        $recipes = $csvData["recipes"];

        // updates the id, created_at and updated_at field
        $recipe['id'] = getNextID($recipes);
        $recipe['created_at'] = date("d/m/Y H:i:s", time());
        $recipe['updated_at'] = date("d/m/Y H:i:s", time());

        // we get the properties for everything except: id, created_at, updated_at
        foreach ($headers as $header) {

            // we don't want to use the received id, created_at, and updated_at
            if (($header == 'id') || ($header == 'created_at') || ($header == 'updated_at')) continue;

            // update it in the array
            $recipe[$header] = $request->input($header);
        }

        // update the csv file
        saveCSV($headers, compact('recipe'), true);

        // we return the new recipe in JSON format
        return $recipe;
    }


}

// a custom helper file, mainly to manipulate csv files
require app('path') . '/helpers.php';