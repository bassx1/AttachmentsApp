<?php

use App\AttachmentsTransferManager;
use App\Clients\Gmail\GmailClient;
use App\Entities\User;

require_once 'bootstrap/autoload.php';

// Just fake that we got users mapped from DB
$user1 = new User();
$user2 = new User();

$client1 = new GmailClient($user1->getId());
$client2 = new GmailClient($user2->getId());


$transferManager = new AttachmentsTransferManager($client1, $client2);
$transferManager->setAllowedMimeTypes('pdf');
$transferManager->run();
