<?php


$connect = connect();
$time = time();

function connect()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "recipies_suggestor";

    return @mysqli_connect($servername, $username, $password, $dbname);
}

function get_chat_response_gemini($userMessage)
{
    $apiKey = "AIzaSyC42nGIgBR7Fri4SNzHV4Oh6T7JtNaH3UQ";

    $favRecipiesFilePath = "./fav_recipes.json";
    $availableIngredients = getAvailableIngredients();
    $rules = '[
        "1. Read \'Favorite recipes\' json file.",
        "2. Read the \'Available ingredients\'.",
        "3. Read \'nUser message\'.",
        "4. Then generate a recipy as users favourite item and with \'available ingredients\'.",
        "5. The Response will be same as Favorite recipes structure, and return only as json. and the response must be followed as \'json structure\'. so that my code can directly extract the output.",
        
    ]';

    $json_structure = '{
        "name": "string",
        "description": "string",
        "ingredients": [
          {
            "name": "string",
            "quantity": "string"
          }
        ],
        "instructions": "string",
        "notes": "string"
      }';


    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;
    $favRecipes = file_exists($favRecipiesFilePath) ? file_get_contents($favRecipiesFilePath) : "[]";
    $input = "instructions from server: '$rules'\nAvailable ingredients: $availableIngredients\nFavorite recipes: $favRecipes\nUser message: '$userMessage'\njson structure: \'$json_structure\'";

    $data = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $input
                    ]
                ]
            ]
        ]
    ];

    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ]
    ]);
    $response = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $responseDecoded = json_decode($response, true);

    if (isset($responseDecoded['candidates'][0]['content']['parts'][0]['text'])) {
        return $responseDecoded['candidates'][0]['content']['parts'][0]['text'];
    }

    return false;
}


function extract_recipies_response_gemini($recipeRawText)
{
    $apiKey = "AIzaSyC42nGIgBR7Fri4SNzHV4Oh6T7JtNaH3UQ";

    $rules = '[
        "1. Read \'User recipe\' input text.",
        "2. Read the \'json structure\'.",   
        "3. from \'User recipe\' text, find out the details for  \'json structure\' and give only the json as output and the structure must be same as \'json structure\'. so that i can directly extract that output in my code. if the structure is not same then i will not be able to extract the output.",        
    ]';


    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $apiKey;
    $json_structure = '{
        "name": "string",
        "description": "string",
        "ingredients": [
          {
            "name": "string",
            "quantity": "string"
          }
        ],
        "instructions": "string",
        "notes": "string"
      }';
    $input = "instructions from server: '$rules'\njson structure: $json_structure\nUser recipe: '" . $recipeRawText . "'";

    $data = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $input
                    ]
                ]
            ]
        ]
    ];

    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ]
    ]);
    $response = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $responseDecoded = json_decode($response, true);

    if (isset($responseDecoded['candidates'][0]['content']['parts'][0]['text'])) {
        return $responseDecoded['candidates'][0]['content']['parts'][0]['text'];
    }

    return false;
}






function createDatabaseWithTableIfNotExists()
{
    global $connect;

    $createDatabaseQuery = "CREATE DATABASE IF NOT EXISTS `recipies_suggestor`";
    $query1 =  @mysqli_query($connect, $createDatabaseQuery);



    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS `available_ingredients` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(1024) NOT NULL,
            quantity VARCHAR(255) NOT NULL,
            time VARCHAR(255) NOT NULL
        )
    ";
    $query2 = @mysqli_query($connect, $createTableQuery);

    return $query1 && $query2;
}


function insertAvailableIngredients($name, $quantity)
{
    global $connect, $time;

    $insertQuery = "INSERT INTO `available_ingredients` (name, quantity, time) VALUES ('$name', '$quantity', '$time')";
    return @mysqli_query($connect, $insertQuery);
}


function getAvailableIngredients()
{
    global $connect;

    $selectQuery = "SELECT * FROM `available_ingredients`";
    $result = @mysqli_query($connect, $selectQuery);

    $ingredients = [];
    foreach ($result as $row) {
        $ingredients[] = $row;
    }

    return $ingredients;
}


function updateAvailableIngredients($id, $name, $quantity)
{
    global $connect, $time;

    $updateQuery = "UPDATE `available_ingredients` SET name='$name', quantity='$quantity', time='$time' WHERE id=$id";
    @mysqli_query($connect, $updateQuery);
}



function getAllFavouriteRecipes()
{
    $favRecipiesFilePath = "./fav_recipes.json";
    return file_exists($favRecipiesFilePath) ? file_get_contents($favRecipiesFilePath) : "[]";
}


function addFavouriteRecipe($name, $description, $ingredients, $instructions, $notes)
{
    if (empty($name) || empty($description) || empty($ingredients) || empty($instructions) || empty($notes)) {
        // echo "hell1";
        return false;
    }

    if (count($ingredients) < 1) {
        // echo "hell2";
        return false;
    }

    $favRecipiesFilePath = "./fav_recipes.json";
    $favRecipes = json_decode(file_exists($favRecipiesFilePath) ? file_get_contents($favRecipiesFilePath) : "[]", true);

    $favRecipes[] = [
        "id" => count($favRecipes),
        "name" => $name,
        "description" => $description,
        "ingredients" => $ingredients,
        "instructions" => $instructions,
        "notes" => $notes
    ];

    file_put_contents($favRecipiesFilePath, json_encode($favRecipes));
    return true;
}

function removeFavoriteRecipe($id)
{
    $favRecipiesFilePath = "./fav_recipes.json";
    $favRecipes = json_decode(file_exists($favRecipiesFilePath) ? file_get_contents($favRecipiesFilePath) : "[]", true);

    $newFavRecipes = [];
    foreach ($favRecipes as $key => $favRecipe) {
        if ($favRecipe['id'] != $id) {
            $newFavRecipes[] = $favRecipe;
        }
    }

    file_put_contents($favRecipiesFilePath, json_encode($newFavRecipes));
    return true;
}



function add_fav_recipe_from_text($user_text)
{
    $result = false;
    while (!$result) {
        $recipeJson = extract_recipies_response_gemini($user_text);
        if ($recipeJson) {
            $recipeArray = json_decode($recipeJson, true);
            if (isset($recipeArray['name'], $recipeArray['description'], $recipeArray['ingredients'], $recipeArray['instructions'], $recipeArray['notes'])) {
                $result = addFavouriteRecipe(
                    $recipeArray['name'],
                    $recipeArray['description'],
                    $recipeArray['ingredients'],
                    $recipeArray['instructions'],
                    $recipeArray['notes']
                );
            }
        }
    }
    return $result;
}
