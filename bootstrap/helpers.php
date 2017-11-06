<?php

use App\Services\Container;

function dd($args)
{
    dump($args);
    exit();
}

function storagePath($filename = null)
{
    return __DIR__ . '/../storage/' . $filename;
}

function rootPath($filename = null)
{
    return __DIR__ . '/../' . $filename;
}


function configPath($filename = null)
{
    return __DIR__ . '/../config/' . $filename;
}

function getFileExtensionByName($filename)
{
    $nameParts = explode('.', $filename);
    return array_pop($nameParts);
}


function DI()
{
    return Container::getInstance();
}
