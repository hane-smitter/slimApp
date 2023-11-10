<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as ReqHandler;
use Slim\Psr7\Response;
use JsonSchema\Validator;

$dataValidation = function (Request $request, ReqHandler $handler) {
    $jsonSchema = <<<'JSON'
{
    "type": "object",
    "properties": {
        "name": {
            "type": "string"
        },
        "team": {
            "type": "string"
        },
        "categorry": {
            "type": "string"
        }
    },
    "required": ["name", "team", "category"]
}
JSON;

    $jsonSchemaObject = json_decode($jsonSchema);

    $contentType = $request->getHeaderLine("Content-Type");

    if (strstr($contentType, "application/json")) {
        $contents = json_decode(file_get_contents("php://input"), true);

        if (json_last_error() == JSON_ERROR_NONE) {
            $request = $request->withParsedBody($contents);
        }
    };

    $validator = new Validator();
    $data = $request->getParsedBody();
    $dataObj = json_decode(json_encode($data)); //  To get object in PHP, note: ommission of 2nd arg(true) in json_decode

    $validator->validate($dataObj, $jsonSchemaObject);


    if ($validator->isValid()) {
        $response = $handler->handle($request);
        return $response;
    } else {
        $response = new Response();
        $response->getBody()->write(json_encode($validator->getErrors()));

        return $response->withHeader("Content-Type", "application/json")->withStatus(400);
    }
};
