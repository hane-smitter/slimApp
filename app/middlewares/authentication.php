<?php

use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Http\Message\ResponseInterface as Response;
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
        sendErrorResponse(["msg" => "No such user"]);
    }

    if (array_key_exists("apikey", $result)) {
        $hashedApiKey = $result["apikey"];
    } else {
        sendErrorResponse(["msg" => "username does not exist"]);
    }


    if (!password_verify($apiKey, $hashedApiKey)) {
        sendErrorResponse([
            "msg" => "Invalid Api Key"
        ]);
    }

    $response = $handler->handle($request);
    return $response;
};

function sendErrorResponse(array $error)
{
    $response = new Response();
    $response->getBody()->write(json_encode($error));

    $failureResponse = $response->withHeader("Content-Type", "application/json")->withStatus((401));

    return $failureResponse;
};
