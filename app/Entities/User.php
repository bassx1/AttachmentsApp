<?php

namespace App\Entities;

use App\Notifications\Interfaces\NotifiableInterface;

/**
 * Dummy User entity with faked autoincrement
 * @package App\Entities
 */
class User implements NotifiableInterface
{

    protected static $autoincrement = 1;
    protected $id;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->id = static::$autoincrement;
        static::$autoincrement += 1;
    }


    public function getId()
    {
        return $this->id;
    }

}