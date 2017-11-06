<?php

namespace App\Storages\Interfaces;


use App\Entities\Attachment;

interface CloudStorageInterface
{
    /**
     * @param Attachment $file
     * @return mixed
     */
    public function uploadFile(Attachment $file);
}