<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . "/../middlewares/jsonBodyParser.php";
require_once __DIR__ . "/../middlewares/authentication.php";


// 1. Home
$app->get('/', function (Request $request, Response $response) {
    $response_array = [
        "message" => "Welcome to Slim PHP API"
    ];

    $response_data = json_encode($response_array);

    $response->getBody()->write($response_data);
    return $response->withHeader("Content-Type", "application/json");
});

// 2. Get all Players
$app->get("/players", function (Request $request, Response $response) {
    // Doctrine used as Database Abstraction Layer(DBAL)
    $queryBuilder = $this->get("DB")->getQueryBuilder();
    $queryBuilder->select("id", "name", "team", "category")->from("players");


    $results = $queryBuilder->executeQuery()->fetchAllAssociative(); // to return the result as an array

    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
});


// 3. Get a single player
$app->get("/player/{id}", function (Request $request, Response $response, array $args) {
    $queryBuilder = $this->get("DB")->getQueryBuilder();
    $queryBuilder->select("id", "name", "team", "category")->from("players")->where("id = ?")->setParameter(1, $args["id"]);

    $results = $queryBuilder->executeQuery()->fetchAssociative();

    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
});

// 4. Add a new player
$app->post("/player/add", function (Request $request, Response $response) {
    $body = $request->getParsedBody();

    // $response->getBody()->write(json_encode($body["name"]));
    // return $response;


    $queryBuilder = $this->get("DB")->getQueryBuilder();

    $queryBuilder->insert("players")->setValue("name", "?")->setValue("team", "?")->setValue("category", "?")
        ->setParameter(1, $body["name"])->setParameter(2, $body["team"])->setParameter(3, $body["category"]);

    $results = $queryBuilder->executeStatement(); // This is an insertion: No data is returned

    $response->getBody()->write(json_encode($results));
    return $response->withHeader("Content-Type", "application/json");
})->add($jsonBodyParser)->add($authentication);

// 5. Update a player
$app->put("/player/{id}", function (Request $request, Response $response, array $args) {
    $body = $request->getParsedBody();

    $queryBuilder = $this->get("DB")->getQueryBuilder();

    $queryBuilder->update("players")->set("name", "?")
        ->set("team", "?")
        ->set("category", "?")
        ->where("id = ?")
        ->setParameter(1, $body["name"])
        ->setParameter(2, $body["team"])
        ->setParameter(3, $body["category"])
        ->setParameter(4, $args["id"]);

        $results = $queryBuilder->executeStatement();
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
})->add($jsonBodyParser)->add($authentication);

// 6. Delete a player
$app->delete("/player/{id}", function(Request $request, Response $response, array $args) {
    $queryBuilder = $this->get("DB")->getQueryBuilder();

    $queryBuilder->delete("players")->where("id = ?")->setParameter(1, $args["id"]);
    $results = $queryBuilder->executeStatement();
    $response->getBody()->write(json_encode($results));

    return $response->withHeader("Content-Type", "application/json");
})->add($authentication);