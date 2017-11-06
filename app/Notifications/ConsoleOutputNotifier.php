<?php

namespace App\Notifications;

use App\Notifications\Interfaces\NotifiableInterface;
use App\Notifications\Interfaces\NotifierInterface;

class ConsoleOutputNotifier implements NotifierInterface
{

    public function notify($message, NotifiableInterface $user = null)
    {
        DI()->get('ConsoleOutputService')->success($message);
    }
}