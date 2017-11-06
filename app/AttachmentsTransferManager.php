<?php

namespace App;

use App\Clients\Interfaces\ClientInterface;
use App\Notifications\Interfaces\NotifierInterface;
use App\Services\ConsoleOutputService;

class AttachmentsTransferManager
{

    protected $attachments;

    /**
     * @var mixed
     */
    protected $allowedMimeTypes;

    /**
     * @var NotifierInterface
     */
    protected $notifier;

    /**
     * @var ConsoleOutputService
     */
    protected $consoleOutputService;

    /**
     * @var ClientInterface
     */
    private $fromClient;

    /**
     * @var ClientInterface
     */
    private $toClient;


    /**
     * AttachmentsTransferManager constructor.
     * @param ClientInterface $from
     * @param ClientInterface $to
     */
    public function __construct(ClientInterface $from, ClientInterface $to)
    {
        $this->fromClient = $from;
        $this->toClient = $to;

        $this->notifier = DI()->get('notifier');
        $this->consoleOutputService = DI()->get('ConsoleOutputService');
    }


    /**
     * @param $mimes array|string
     * @return $this
     */
    public function setAllowedMimeTypes($mimes)
    {
        $this->allowedMimeTypes = (array)$mimes;
    }

    /**
     * Upload files from "user1" mail attachments to "user2" cloud storage
     */
    public function run()
    {
        $this->consoleOutputService->writeln('Start fetching attachments...');

        try {
            $attachments = $this->fromClient->fetchAttachments($this->allowedMimeTypes);
            $this->notifier->notify('Notifying first user that '. count($attachments) .' of his attachments fetched');
        } catch (\Exception $e) {
            dump($e->getMessage());
            exit;
        }

        $this->consoleOutputService->writeln('Start uploading files to storage...');

        try {
            /** @var Attachment $file */
            $storage = $this->toClient->getStorage();
            foreach ($attachments as $file) {
                $storage->uploadFile($file);
            }

            $this->notifier->notify(
                'Notifying second user that he got ' . count($attachments) . ' new attachments in his storage'
            );

        } catch (\Exception $e) {
            dump('Something when wrong while uploading attachments to storage');
            dump($e->getMessage());
            exit;
        }

    }


}