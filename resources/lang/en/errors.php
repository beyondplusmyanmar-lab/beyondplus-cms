<?php

return [
    'home' => 'Back to home',
    'back' => 'Go back',

    '401' => [
        'title'   => 'Unauthorized',
        'message' => 'You need to sign in before you can view this page.',
    ],
    '403' => [
        'title'   => 'Access forbidden',
        'message' => "You don't have permission to view this page.",
    ],
    '404' => [
        'title'   => 'Page not found',
        'message' => "The page you're looking for doesn't exist, was moved, or the link is broken.",
    ],
    '419' => [
        'title'   => 'Page expired',
        'message' => 'Your session expired for security reasons. Please refresh the page and try again.',
    ],
    '429' => [
        'title'   => 'Too many requests',
        'message' => "You've made too many requests in a short time. Please wait a moment and try again.",
    ],
    '500' => [
        'title'   => 'Something went wrong',
        'message' => 'An unexpected error occurred on our end. Please try again in a little while.',
    ],
    '503' => [
        'title'   => 'Be right back',
        'message' => "We're down for a short spell of maintenance. Please check back shortly.",
    ],
];
