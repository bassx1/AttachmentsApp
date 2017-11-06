<?php

namespace App\Storages;

use App\Storages\Interfaces\CloudStorageInterface;
use Google_Service_Drive_DriveFile as GoogleServiceDriveFile;
use Google_Service_Drive as GoogleServiceDrive;
use Google_Client as GoogleClient;
use App\Entities\Attachment;

class DriveStorage implements CloudStorageInterface
{
    protected $driveService;

    /**
     * DriveStorage constructor.
     * @param GoogleClient $client
     */
    public function __construct(GoogleClient $client)
    {
        $this->driveService = new GoogleServiceDrive($client);
    }


    public function uploadFile(Attachment $file)
    {
        $fileMetadata = new GoogleServiceDriveFile([
            'name' => $file->getName()
        ]);

        // Converting gmail encoded content to standard RFC 4648 base64-encoding and decode it
        $data =  base64_decode(strtr($file->getContent(), ['-' => '+', '_' => '/']));

        return $this->driveService->files->create($fileMetadata, [
            'data' => $data,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);
    }

}