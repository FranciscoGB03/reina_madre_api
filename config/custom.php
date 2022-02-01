<?php
return [
    'jwt_base64_key' => env('JWT_BASE6_KEY'),
    'minutes_atoken_exp' => env('MINUTES_ATOKEN_EXP'),
    'days_rtoken_exp' => env('DAYS_RTOKEN_EXP'),
    'client_url' => env('CLIENT_URL'),
    'basic_aut_user' => env('BASIC_AUTH_USER', 'xheim'),
    'basic_aut_password' => env('BASIC_AUTH_PASSWORD', 'Xheim2020.'),

    'cl_cloudname' => env('CL_CLOUDNAME'),
    'cl_apikey' => env('CL_APIKEY'),
    'cl_apisecret' => env('CL_APISECRET'),
    'cl_secure' => env('CL_SECURE'),
];
