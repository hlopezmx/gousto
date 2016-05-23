#Recipes API Test

##1. HOW TO USE THE SOLUTION

The requested API has been developed using the Lumen micro-framework.

###1.1 USING THE API


It includes the following routes:

| Verb | Path                         | Controller                             | Action              |
|------|------------------------------|----------------------------------------|---------------------|
| GET  | /recipes/{id}                | App\Http\Controllers\RecipesController | getRecipeById       |
| GET  | /recipes/cuisines/{cuisine}  | App\Http\Controllers\RecipesController | getRecipesByCuisine |
| POST | /recipes/{id}/rates/{rating} | App\Http\Controllers\RecipesController | rateRecipe          |
| PUT  | /recipes/{id}                | App\Http\Controllers\RecipesController | updateRecipe        |
| POST | /recipes                     | App\Http\Controllers\RecipesController | addRecipe           |

So, some examples of how to access it via browser are:
  1. http://sampledomain.com/recipes/3 to return the recipe with id 3 in JSON format.
  2. http://sampledomain.com/recipes/cuisines/british?page=2 to return the second page of british cuisine recipes. Note the page size is set to 2 recipes.


###1.2 USING THE SOURCE CODE

* Get the API source code
```
	git clone https://github.com/hlopezmx/gousto.git
```
* Update the libraries using: 
```
composer update
```
* Give permissions to the storage folder (_chmod -R 777 storage_), as CSV data files are stored in the _/storage/data_ folder. 

* if needed, add the .htacess file at the project's root, with content like this:

```
RewriteEngine On

RewriteCond %{THE_REQUEST} /public/([^\s?]*) [NC]

RewriteRule ^ %1 [L,NE,R=302]

RewriteRule ^((?!public/).*)$ public/$1 [L,NC]
```

###1.3 UNIT TESTING

The solution includes a testing file (/tests/GoustoAPITest.php) with 6 tests: one for each route, plus another to test the CSV importing. Some tests include more than one assertion, to test both sucesses and expected errors.

To execute the unit testing, just run phpunit in the project folder. Below is the sample output showing successful testing:

```
C:\Users\hlt\PhpstormProjects\gousto>vendor\bin\phpunit

PHPUnit 4.8.24 by Sebastian Bergmann and contributors.

......

Time: 1.05 seconds, Memory: 8.75Mb
 
OK (6 tests, 11 assertions)
```

	
##2. ABOUT THE FRAMEWORK SELECTION
The solution has been developed using the Lumen micro-framework v5.2. It has been selected because it is based in the Laravel framework, but optimised for micro-services and APIs. As described in Lumen's documentation: _"Lumen 5.2 represents a shift on slimming Lumen to focus solely on serving stateless, JSON APIs. As such, sessions and views are no longer included with the framework"_, improving its performance.

I consider Laravel & Lumen are very powerful frameworks. Moreover, I understand that your monolithic application has been developed in Laravel, so by choosing a Laravel based framework, my intention is to demonstrate my capability to translate your existent code into a different technology if needed, to evolve into a micro-services architecture, for example.


##3. HOW THIS API FEEDS MULTIPLE CONSUMERS

The REST API makes use of standard HTTP protocol and verbs (e.g. GET, POST, PUT, ...), facilitating CRUD activities. In addition, the returned data is encoded in JSON. These features are used practicaly by any modern tool (e.g. other websites, mobiles, etc.). 

In the proposed API, when data is returned, it includes all the information for the recipe(s). In the event that a consumer only requires part of it (e.g. only title and calaories), the rest could be simply dismissed.

On the other hand, when a recipe is being updated/created, the API doesn't force the caller to send data for all the recipe properties. This way the API consumers won't have to be worried about sending data they don't handle. In the case of the ratings, the API forces to use numeric values between 1 and 5.
	
##4. NOTES TO CONSIDER

**4.1** The MVC architecture includes modeling the data. Alhough I created a Recipe class (\App\Recipe) to comply with this, it is just a collection of properties without logic attached to it, because this is only a simplistic scenario for the API. At the end, not having a database and therefore not making a proper use of eloquent, but instead loading the data from the CSV into an array and then instantiating the objects to immediately translate them to JSON didn't make much sense. On the other hand, mapping from the CSV into an array with keys was stright forward.

Having in mind that micro-services are intended to provide fast, simple, reliable and scalable solutions, I have opted to not use the Recipe class and work directly with the loaded array in the Controller which is faster.

However, I have kept the Recipe class and created one route to serve as a proof of concept:

| Verb | Path                         | Controller                             | Action                        |
|------|------------------------------|----------------------------------------|-------------------------------|
| GET  | /recipes2/{id}               | App\Http\Controllers\RecipesController | getRecipeObjectById           |



Being a scalable solution, when the business logic and requiremens evolve into something more complicated, it will be viable to make use and extend the Recipe model.

**4.2** An extra CSV file has been included (_/storage/data/ratings.csv_) to store the ratings received through _/recipes/{id}/rates/{rating}_.


##5. HOW CAN THIS BE IMPROVED?

* If the application evolves, it would make sense to make use of the Recipe Model.

* Add security measurements, specially when inserting/updating data.

* Add exceptions handling, specially for the csv file interaction.

* Use a framework compatible with an autoscaling solution for micro-services (e.g. Node.js for AWS Lamda)

* Include logging to events.

* When using pagination, include the page number and links to the previous and next pages.
