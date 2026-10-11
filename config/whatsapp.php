<?php

return [
    'base_url'        => env('EVOLUTION_BASE_URL', 'http://195.35.24.73:4000'),
    'instance_id'     => env('EVOLUTION_INSTANCE_ID'),
    'instance_token'  => env('EVOLUTION_INSTANCE_TOKEN'),
    'global_api_key'  => env('EVOLUTION_GLOBAL_API_KEY'),
    'webhook_url'     => env('EVOLUTION_WEBHOOK_URL'),
    'timeout'         => env('EVOLUTION_TIMEOUT', 30),
];