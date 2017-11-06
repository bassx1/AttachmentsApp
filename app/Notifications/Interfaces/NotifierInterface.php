<?php

namespace App\Notifications\Interfaces;


interface NotifierInterface
{
    /**
     * Maybe some day we'll need to send notification via email or something else
     * so let's keep NotifiableInterface $user as param
     *
     * @param $message
     * @param NotifiableInterface|null $user
     * @return mixed
     */
    public function notify($message, NotifiableInterface $user = null);
}