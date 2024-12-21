# API Documentation for Mofa's Kitchen Buddy

This document provides detailed information about the API endpoints available in the system. The base URL for all endpoints is:

```
http://localhost/json
```

## Endpoints

### 1. Add Ingredient
**Method:** POST  
**Endpoint:** `/json`  
**Description:** Adds a new ingredient to the database.

#### Request Parameters:
- `add_ingredient` (required): Set to any value to trigger this operation.
- `name` (required): Name of the ingredient.
- `quantity` (required): Quantity of the ingredient.

#### Example Request:
```json
{
  "add_ingredient": true,
  "name": "Sugar",
  "quantity": "2kg"
}
```

#### Example Response:
```json
{
  "ingredient_added": true
}
```

---

### 2. Get Ingredients
**Method:** POST  
**Endpoint:** `/json`  
**Description:** Retrieves all available ingredients from the database.

#### Request Parameters:
- `get_ingredients` (required): Set to any value to trigger this operation.

#### Example Request:
```json
{
  "get_ingredients": true
}
```

#### Example Response:
```json
{
  "ingredients": [
    {
      "id": 1,
      "name": "Sugar",
      "quantity": "2kg",
      "time": "1672531200"
    }
  ]
}
```

---

### 3. Update Ingredient
**Method:** POST  
**Endpoint:** `/json`  
**Description:** Updates an existing ingredient in the database.

#### Request Parameters:
- `update_ingredient` (required): Set to any value to trigger this operation.
- `id` (required): ID of the ingredient to update.
- `name` (required): Updated name of the ingredient.
- `quantity` (required): Updated quantity of the ingredient.

#### Example Request:
```json
{
  "update_ingredient": true,
  "id": 1,
  "name": "Brown Sugar",
  "quantity": "1.5kg"
}
```

#### Example Response:
```json
{
  "ingredient_updated": true
}
```

---

### 4. Get Favourite Recipes
**Method:** POST  
**Endpoint:** `/json`  
**Description:** Retrieves a list of all favourite recipes.

#### Request Parameters:
- `get_favourite_recipes` (required): Set to any value to trigger this operation.

#### Example Request:
```json
{
  "get_favourite_recipes": true
}
```

#### Example Response:
```json
{
  "favourite_recipes": [
    {
      "id": 1,
      "name": "Pancakes",
      "description": "Fluffy and delicious pancakes.",
      "ingredients": [
        { "name": "Flour", "quantity": "200g" },
        { "name": "Milk", "quantity": "250ml" }
      ],
      "instructions": "Mix ingredients and cook.",
      "notes": "Serve with syrup."
    }
  ]
}
```

---

### 5. Remove Favourite Recipe
**Method:** POST  
**Endpoint:** `/json`  
**Description:** Removes a recipe from the favourite list.

#### Request Parameters:
- `remove_favourite_recipe` (required): Set to any value to trigger this operation.
- `id` (required): ID of the recipe to remove.

#### Example Request:
```json
{
  "remove_favourite_recipe": true,
  "id": 1
}
```

#### Example Response:
```json
{
  "favourite_recipe_removed": true
}
```

---

### 6. Add Favourite Recipe
**Method:** POST  
**Endpoint:** `/json`  
**Description:** Adds a recipe to the favourite list from raw text.

#### Request Parameters:
- `add_favourite_recipe` (required): Raw text of the recipe to add.

#### Example Request:
```json
{
  "add_favourite_recipe": "Pancakes: 200g Flour, 250ml Milk, mix and cook."
}
```

#### Example Response:
```json
{
  "add_favourite_recipe": true
}
```

---

### 7. Recipe Suggestion
**Method:** POST  
**Endpoint:** `/json`  
**Description:** Generates a recipe suggestion using available ingredients and favourite recipes.

#### Request Parameters:
- `get_recipe_suggest` (required): User message or hint for recipe suggestion.

#### Example Request:
```json
{
  "get_recipe_suggest": "Suggest a dessert."
}
```

#### Example Response:
```json
{
  "recipe_suggest": {
    "name": "Chocolate Cake",
    "description": "A rich chocolate dessert.",
    "ingredients": [
      { "name": "Cocoa Powder", "quantity": "50g" },
      { "name": "Sugar", "quantity": "100g" }
    ],
    "instructions": "Mix ingredients and bake.",
    "notes": "Serve chilled."
  }
}
