<?php

namespace App\Clients\Interfaces;


use App\Storages\Interfaces\CloudStorageInterface;

interface ClientInterface
{
    /**
     * @param null $allowedMimeTypes
     * @return array
     */
    public function fetchAttachments($allowedMimeTypes = null);

    /**
     * @return CloudStorageInterface
     */
    public function getStorage();
}