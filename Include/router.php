<?php

declare(strict_types=1);


const ALLOW_METHODS = ['GET', 'POST'];
const INDEX_URI = '';


const INDEX_ROUNTE = 'home';



function normalizeUri(string $uri): string
{

    $uri = strtolower(trim($uri, '/'));


    return $uri == INDEX_URI ? INDEX_ROUNTE : $uri;
}


function notFound()
{
    http_response_code(404);

    renderView('404');
    exit;
}


function getFilePath(string $uri): string
{
    return ROUTE_DIR . '/' . normalizeUri($uri) . '.php';
}


function dispatch(string $uri, string $method): void
{

    $uri = normalizeUri($uri);


    if (!in_array(strtoupper($method), ALLOW_METHODS)) {
        notFound();
    }


    $filePath = getFilePath($uri);
    if (file_exists($filePath)) {
        include($filePath);
        return;
    } else {
        notFound();
    }
}
