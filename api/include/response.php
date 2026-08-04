<?php

function jsonResponseOk($data = array())
{
    http_response_code(200);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array(
        "success" => true,
        "data" => $data,
        "message" => "Ok"
    ));
    exit;
}

function jsonResponseBadRequest($message = "Bad Request")
{
    http_response_code(400);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array(
        "success" => false,
        "data" => null,
        "message" => $message
    ));
    exit;
}

function jsonResponseUnauthorized($message = "Unauthorized")
{
    http_response_code(401);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array(
        "success" => false,
        "data" => null,
        "message" => $message
    ));
    exit;
}

function jsonResponseInternalServerError($message = "Internal Server Error")
{
    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(array(
        "success" => false,
        "data" => null,
        "message" => $message
    ));
    exit;
}
