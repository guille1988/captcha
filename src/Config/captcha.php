<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable test keys
    |--------------------------------------------------------------------------
    |
    | By default the package comes with a test key, that always passes
    | verification only when application is not in production. To disable this
    | configuration and always use non-test key, you can put false.
    |
    */

    'enable_test_key' => true,

    /*
    |--------------------------------------------------------------------------
    | Secret Key
    |--------------------------------------------------------------------------
    |
    | This is the CAPTCHA key that only back-end has, please fill it
    | with your own.
    |
    */

    'secret_key' => '',

    'test_secret_key' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',

    /*
    |--------------------------------------------------------------------------
    | URL
    |--------------------------------------------------------------------------
    |
    | The URL to post google to make a verification of CAPTCHA.
    |
    */

    'url' => 'https://www.google.com/recaptcha/api/siteverify',

    /*
    |--------------------------------------------------------------------------
    | Threshold (only for CAPTCHA V3)
    |--------------------------------------------------------------------------
    |
    | This value is sent by CAPTCHA and represents the probability
    | of the request to be a bot, being 0.1 the lowest and 1 the highest.
    | The recommended value is 0.5, but you can customize it according
    | to your needs.
    |
    */

    'threshold' => 0.5,

    /*
    |--------------------------------------------------------------------------
    | Error codes
    |--------------------------------------------------------------------------
    |
    | This are all error codes from the API. You can add more, customize
    | responses or remove some according to your needs. Threshold error
    | code is only for CAPTCHA V3 and default one is a fallback if no
    | error got matched.
    |
    */

    'error_codes' => [
        'invalid-request' => 'Server is not responding',
        'missing-input-secret' => 'The secret parameter is missing',
        'invalid-input-secret' 	 => 'The secret parameter is invalid or malformed',
        'missing-input-response' =>	'The response parameter is missing',
        'invalid-input-response' =>	'The response parameter is invalid or malformed',
        'bad-request' => 'The request is invalid or malformed',
        'timeout-or-duplicate' => 'The response is no longer valid: either is too old or has been used previously',
        'threshold-surpassed' => 'Threshold surpassed',
        'default' => 'An unknown error has occurred'
    ]
];