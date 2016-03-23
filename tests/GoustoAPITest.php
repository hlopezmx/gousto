<?php

/**
 * Created by PhpStorm.
 * User: Hugo Lopez Tovar
 * Date: 22/03/2016
 * Time: 9:02
 */
class GoustoAPITest extends TestCase
{

    public function testCSVLoad()
    {
        $cont = new \App\Http\Controllers\RecipesController();
        $response = loadCSV();
        $this->assertNotEmpty($response);
    }


    /**
     *  1) Fetch a recipe by id
     */
    public function testFetchRecipeById()
    {
        $this->get('/recipes/1')
            ->seeJson([
                'slug' => 'sweet-chilli-and-lime-beef-on-a-crunchy-fresh-noodle-salad'
            ]);
    }

    /**
     * 2) Fetch all recipes for a specific cuisine
     */
    public function testFetchRecipesByCuisine()
    {
        // we ask for british, we get british
        $this->get('/recipes/cuisines/british')
            ->seeJson([
                'recipe_cuisine' => 'british'
            ]);


        // we ask for non existent page
        $this->get('/recipes/cuisines/british?page=9999')
            ->seeJson([
                "error" => "No more pages to show for british cuisine."
            ]);
    }

    /**
     * 3) Rate an existing recipe between 1 and 5
     */
    public function testRateRecipe()
    {
        // test a valid rating
        $this->post('recipes/3/rates/4')
            ->seeJson([
                "result" => 'Recipe 3 has been rated as 4'
            ]);

        // test an invalid rating (non numeric)
        $this->post('recipes/3/rates/good')
            ->seeJson([
                "error" => 'Rate must be a number between 1 and 5.'
            ]);

        // test an invalid rating (invalid number)
        $this->post('recipes/3/rates/10')
            ->seeJson([
                "error" => 'Rate must be a number between 1 and 5.'
            ]);

        // test an valid rating for an non existent recipe
        $this->post('recipes/9999/rates/4')
            ->seeJson([
                "error" => 'Recipe not found.'
            ]);
    }

    /**
     * 4) Update an existing recipe
     */
    public function testUpdateRecipe()
    {
        // try to update a recipe that does exist
        $this->put('recipes/3', ['bulletpoint1' => 'data 1', 'bulletpoint2' => 'data 2'])
            ->seeJson([
                "bulletpoint1" => 'data 1'
            ]);

        // try to update a recipe that doesn't exist
        $this->put('recipes/99999', ['bulletpoint1' => 'data 1', 'bulletpoint2' => 'data 2'])
            ->seeJson([
                "error" => 'Recipe not found.'
            ]);

    }

    /**
     * 5) Store a new recipe
     */
    public function testNewRecipe()
    {
        $this->post('recipes',
            [
                'box_type' => 'gourmet',
                'title' => 'unit testing with rice',
                'slug' => 'unit-testing-with-rice',
                'recipe_cuisine' => 'mexican',
                'gousto_reference' => '123',
            ])
            ->seeJson([
                "slug" => 'unit-testing-with-rice'
            ]);

    }

}
