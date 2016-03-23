<?php


/**
 * Loads the given CSV file into an array with keys
 *
 * @return a bidimensional array, composed of the header array and the recipes data array
 */
function loadCSV()
{
    // the real path...
    $filename = storage_path() . "/data/recipes.csv";

    // does the file exist?
    if (!file_exists($filename)) return false;

    // open the file as read only
    $csvData = fopen($filename, 'rb');
    while (!feof($csvData)) {

        if (!isset($header)) {
            $header = fgetcsv($csvData);      // our fist row is the header
        } else {
            $data[] = fgetcsv($csvData);        // the rest is data
        }
    }

    // structures each data row. In other words, maps the field to its value, which is useful for JSON output
    if (isset($data)) {
        foreach ($data as $dataRow) {

            // prepare a new array for this row id (column 0)
            $recipes[$dataRow[0]] = [];

            // maps each property to its value
            $iProperty = 0;
            foreach ($header as $property) {
                $recipes[$dataRow[0]][$property] = $dataRow[$iProperty++];
            }
        }
    }

    // if there are no headers, nor data, then we return false
    if ((!isset($header)) && (!isset($recipes)))
        return false;
    else
        return compact('header', 'recipes');
}

/**
 * Stores the $recipes collection of recipes into the given file
 *
 * @param $header - the list of fields
 * @param $recipes - the collection of recipes, even if it is a single recipe being updated, it must be a collection
 * @param $append - binary flag to append (true) or overwrie (false)
 */
function saveCSV($header, $recipes, $append)
{
    // we had removed the first row in the array because it had the fields and not data
    // meaning we don't have an array index of 0
    // so, to make the iteration easier, we get the values again to reindex starting from zero
    $recipes = array_values($recipes);

    // the real path...
    $filename = storage_path() . '/data/recipes.csv';

    if ($append)
        // open the file in append/create mode
        $file = fopen($filename, 'ab');
    else
        // open the file in overwrite/create mode
        $file = fopen($filename, 'wb');

    if ($recipes) {
        // write headers to the file, we take the keys from the first data row
        if (!$append) fputcsv($file, $header);

        // write data to the file
        foreach ($recipes as $recipe) {

            // we only want the values in their current order
            $recipeValues = array_values($recipe);

            // add the csv line to the file
            fputcsv($file, $recipeValues);
        }
    }
    fclose($file);
}

/**
 * Appends the rating given to the recipe, into the ratings.csv file
 *
 * @param $id
 * @param $rating
 */
function saveRatesCSV($id, $rating)
{
    // the real path...
    $filename = storage_path() . "/data/ratings.csv";

    // open the file in append mode
    $file = fopen($filename, 'ab');

    // add the csv line to the file
    fputcsv($file, compact('id', 'rating'));

    fclose($file);
}


/**
 * calculates the ID to be used when inserting a recipe
 *
 * @param $recipes - array of recipes
 * @return int - the new ID to be used
 */
function getNextID($recipes)
{
    return max(array_keys($recipes)) + 1;
}

