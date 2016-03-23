<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| The following routes are implemented
|   1) Fetch a recipe by id
|       GET /recipes/{id}
|
|   2) Fetch all recipes for a specific cuisine (should paginate)
|       GET /recipes/cuisines/{cuisine}
|
|   3) Rate an existing recipe between 1 and 5
|       POST /recipes/{id}/rates/{rating}
|
|   4) Update an existing recipe
|   	PUT /recipes/{id}
|
|   5) Store a new recipe
|   	POST /recipes
|
*/


// 2) Fetch all recipes for a specific cuisine (should paginate)
$app->get('recipes/cuisines/{cuisine}', 'RecipesController@getRecipesByCuisine');

// 2a) This method is just to capture when a cuisine was not given, otherwise, the returned message 'recipe not found' would be confusing.
$app->get('recipes/cuisines/', 'RecipesController@getRecipesByCuisineNotDefined');


// 1) Fetch a recipe by id
//      1.1) Keeping it simple in the controller
$app->get('recipes/{id}', 'RecipesController@getRecipeById');

//      1.2) Using the Recipe class
$app->get('recipes2/{id}', 'RecipesController@getRecipeObjectById');


// 3) Rate an existing recipe between 1 and 5
$app->post('recipes/{id}/rates/{rating}', 'RecipesController@rateRecipe');


// 4) Update an existing recipe
$app->put('recipes/{id}', 'RecipesController@updateRecipe');


// 5) Store a new recipe
$app->post('recipes', 'RecipesController@addRecipe');

