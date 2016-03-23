<?php
/**
 * Created by PhpStorm.
 * User: hlt
 * Date: 22/03/2016
 * Time: 22:19
 */

namespace App;


/**
 * Class Recipe, reproducing the properties found in the csv data file
 * @package App
 */
class Recipe
{

    public $id;
    public $created_at;
    public $updated_at;
    public $box_type;
    public $title;
    public $slug;
    public $short_title;
    public $marketing_description;
    public $calories_kcal;
    public $protein_grams;
    public $fat_grams;
    public $carbs_grams;
    public $bulletpoint1;
    public $bulletpoint2;
    public $bulletpoint3;
    public $recipe_diet_type_id;
    public $season;
    public $base;
    public $protein_source;
    public $preparation_time_minutes;
    public $shelf_life_days;
    public $equipment_needed;
    public $origin_country;
    public $recipe_cuisine;
    public $in_your_box;
    public $gousto_reference;


    /**
     * Returns a JSON string denoting the object properties
     *
     * @return string
     */
    public function __toString()
    {
        // convert the properties to array, to then encode it to JSON format
        $recipeProperties = array_keys(get_class_vars(get_class($this)));
        foreach ($recipeProperties as $key) {
            $arr[$key] = $this->$key;
        }
        return (string)json_encode($arr);
    }


    /**
     * Creates and returns an object of this class based in a CSV line
     *
     * @param $csvLine
     * @return Recipe
     */
    public static function populate($csvLine)
    {
        $recipe = new self;
        $values = array_values($csvLine);

        $recipeProperties = array_keys(get_class_vars(get_class($recipe)));
        $iValue = 0;
        foreach ($recipeProperties as $property) {
            $recipe->$property = $values[$iValue++];
        }

        return $recipe;
    }
}