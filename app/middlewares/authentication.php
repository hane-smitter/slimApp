<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as ReqHandler;
use Slim\Psr7\Response;

$authentication = function (Request $request, ReqHandler $handler) {
    $userName = $request->getHeaderLine(("X-API-USER"));
    $apiKey = $request->getHeaderLine(("X-API-KEY"));

    if (!$userName || !$apiKey) {
        return sendErrorResponse([
            "msg" => "Username and API key required for authentication!"
        ]);
    }

    $queryBuilder = $this->get("DB")->getQueryBuilder();
    $queryBuilder->select("apikey")->from("users")
        ->where("username = ?")
        ->setParameter(1, $userName);

    $result = $queryBuilder->executeQuery()->fetchAssociative();

    if (!$result) {
        return sendErrorResponse(["msg" => "No such user"]);
    }

    if (array_key_exists("apikey", $result)) {
        $hashedApiKey = $result["apikey"];
    } else {
        return sendErrorResponse(["msg" => "username does not exist"]);
    }

    $passMatch = password_verify($apiKey, $hashedApiKey);
    // var_dump(["apiKey" => $apiKey, "hashedApiKey" => $hashedApiKey, "equality" => $passMatch, "Opp.equality" => !$passMatch]);

    if (!$passMatch) {
        return sendErrorResponse([
            "msg" => "Invalid Api Key"
        ]);
    }

    $response = $handler->handle($request);
    return $response;
};

function sendErrorResponse(array $error, int $status = 401)
{
    $response = new Response();
    $response->getBody()->write(json_encode($error));

    $failureResponse = $response->withHeader("Content-Type", "application/json")->withStatus(($status));

    return $failureResponse;
};
