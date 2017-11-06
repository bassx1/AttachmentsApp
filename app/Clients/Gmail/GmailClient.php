<?php

namespace App\Clients\Gmail;

use App\Entities\Attachment;
use App\Clients\Interfaces\ClientInterface;
use App\Storages\DriveStorage;
use Google_Client as GoogleClient;
use Google_Service_Drive as GoogleServiceDrive;

use Google_Service_Gmail as GoogleServiceGmail;
use Google_Service_Gmail_Message as GoogleServiceGmailMessage;
use Google_Service_Gmail_MessagePart as GoogleServiceGmailMessagePart;

class GmailClient implements ClientInterface
{

    const APPLICATION_NAME = 'Attachments app';

    public $client;
    protected $driveService;

    /**
     * Setup an authorized API client.
     * @param $userId
     * @internal param $clientIdPath
     * @throws \Google_Exception
     * @throws \InvalidArgumentException
     */
    public function __construct($userId)
    {
        $this->client = new GoogleClient();

        $this->client->setApplicationName(self::APPLICATION_NAME);
        $this->client->setAccessType('offline');
        $this->client->setScopes([
            GoogleServiceGmail::MAIL_GOOGLE_COM,
            GoogleServiceDrive::DRIVE
        ]);

        $this->authenticate($userId);
    }


    public function authenticate($userId)
    {
        $this->client->setAuthConfig('app_credentials.json');

        // Load previously authorized credentials from a file.
        $credentialsPath = storagePath("gmail_credentials/{$userId}_credentials.json");

        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $this->client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }

        $this->client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($this->client->getAccessToken()));
        }
    }


    /**
     * @param array|null $allowedMimeTypes
     * @return array
     */
    public function fetchAttachments($allowedMimeTypes = null)
    {
        $user = 'me';
        $service = new GoogleServiceGmail($this->client);

        $messagesList = $service->users_messages->listUsersMessages($user)->getMessages();

        $files = [];
        /** @var GoogleServiceGmailMessage $message */
        foreach ($messagesList as $message) {

            $parts = $service->users_messages->get('me', $message->getId())->getPayload()->getParts();

            /** @var GoogleServiceGmailMessagePart $part */
            foreach ($parts as $part) {

                if (!$filename = $part->getFilename()) {
                    continue;
                }

                $extension = getFileExtensionByName($filename);

                if ($allowedMimeTypes === null || in_array($extension, $allowedMimeTypes, true)) {
                    $id = $part->getBody()->getAttachmentId();
                    $attachment = $service->users_messages_attachments->get($user, $message->getId(), $id);

                    $file = new Attachment();
                    $file->setName($filename);
                    $file->setContent($attachment->getData());

                    $files[] = $file;
                }

            }
        }

        return $files;
    }


    /**
     * @return DriveStorage
     */
    public function getStorage()
    {
        if (!$this->driveService) {
            $this->driveService = new DriveStorage($this->client);
        }

        return $this->driveService;
    }
}