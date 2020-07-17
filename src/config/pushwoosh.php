<?php

/*
 * This file is part of Laravel Pushwoosh.
 *
 * (c) Schimpanz Solutions AB <info@schimpanz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Pushwoosh Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'app' => env('PUSHWOOSH_APP_ID', ''),
            'token' => env('PUSHWOOSH_API_KEY', ''),
        ],

        'alternative' => [
            'app' => 'your-application-code',
            'token' => 'your-access-token',
        ],

    ],

];
