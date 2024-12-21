<?php

$result = new ArrayObject();


if (createDatabaseWithTableIfNotExists()) {
    if (isset($_POST['add_ingredient'], $_POST['name'], $_POST['quantity'])) {
        $name = addslashes(strip_tags($_POST['name']));
        $quantity = addslashes(strip_tags($_POST['quantity']));
        $result['ingredient_added'] = insertAvailableIngredients($name, $quantity);
    }
    if (isset($_POST['get_ingredients'])) {
        $result['ingredients'] = getAvailableIngredients();
    }
    if (isset($_POST['update_ingredient'], $_POST['id'], $_POST['name'], $_POST['quantity'])) {
        $id = addslashes(strip_tags($_POST['id']));
        $name = addslashes(strip_tags($_POST['name']));
        $quantity = addslashes(strip_tags($_POST['quantity']));
        $result['ingredient_updated'] = updateAvailableIngredients($id, $name, $quantity);
    }
    if (isset($_POST['get_favourite_recipes'])) {
        $result['favourite_recipes'] = getAllFavouriteRecipes();
    }
    if (isset($_POST['remove_favourite_recipe'], $_POST['id'])) {
        $id = addslashes(strip_tags($_POST['id']));
        $result['favourite_recipe_removed'] = removeFavoriteRecipe($id);
    }

    if (isset($_POST['add_favourite_recipe'])) {
        $_POST['add_favourite_recipe'] = addslashes(strip_tags($_POST['add_favourite_recipe']));
        $result['add_favourite_recipe'] = add_fav_recipe_from_text($_POST['add_favourite_recipe']);
    }

    if(isset($_POST['get_recipe_suggest'])){
        $_POST['get_recipe_suggest'] = addslashes(strip_tags($_POST['get_recipe_suggest']));
        $result['recipe_suggest'] = get_chat_response_gemini($_POST['get_recipe_suggest']);
    }
} else {
    $result['error'] = "Database not created";
}





echo json_encode($result);
