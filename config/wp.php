<?php

return [

    // 'theme_path' => null,

    // 'theme_url'  => null,

    'abort' => [

        'default_response_code' => 500,

        /*
        |-----------------------------------------------------
        | Abort Handler Class
        |-----------------------------------------------------
        |
        | Define the class responsible for handling abort logic in WordPress.
        | This class must implement the AbortHandlerInterface contract.
        | You may replace this with a custom implementation if needed.
        |
        */
        'handler' => \App\Services\WordPress\AbortHandler::class,

        /*
        |-----------------------------------------------------
        | Default Error Responses
        |-----------------------------------------------------
        |
        | Here you can define custom titles and messages for HTTP error status
        | codes. These responses will be used when aborting with specific codes.
        | You can localize these messages using the WordPress __() function.
        |
        */
        'responses' => [

            400 => [
                'title'   => 'Bad Request',
                'message' => 'The request could not be understood by the server.',
            ],

            403 => [
                'title'   => 'Access Denied',
                'message' => 'You are not authorized to access this page.',
            ],

            404 => [
                'title'   => 'Not Found',
                'message' => 'The requested page could not be found.',
            ],

            500 => [
                'title'   => 'An Error Occurred',
                'message' => 'The server returned a "500 Internal Server Error".',
            ],

        ],

    ],

];
