<?php

namespace App\Services;

class ConsoleOutputService
{

    public function writeln($message)
    {
        echo $message . "\n";
    }

    public function success($message)
    {
        echo "\033[32m {$message} \033[0m \n";
    }


}