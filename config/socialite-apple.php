<?php

return [
    'cache' => [
        // Cache store to use. Takes default cache store of project if value set to null.
        'store'  => null,

        // Prefix to cache keys.
        'prefix' => 'apple_public_key_',

        // One week
        'ttl'    => 604800,
    ]
];