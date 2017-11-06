<?php

return [
    'notifier' => DI\object('App\Notifications\ConsoleOutputNotifier'),
    'ConsoleOutputService' => DI\object('App\Services\ConsoleOutputService')
];