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
    | This is the CAPTCHA key that only back-end has.
    |
    */

    'secret_key' => '6Lc1QoQeAAAAAAWLsvJav14ZtVHBNmBo9geZn9d0',

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
    | Error codes
    |--------------------------------------------------------------------------
    |
    | This are all error codes from the API. You can add more, customize
    | responses or remove some according to your needs.
    |
    */

    'error_codes' => [
        'missing-input-secret' => 'The secret parameter is missing',
        'invalid-input-secret' 	 => 'The secret parameter is invalid or malformed',
        'missing-input-response' =>	'The response parameter is missing',
        'invalid-input-response' =>	'The response parameter is invalid or malformed',
        'bad-request' => 'The request is invalid or malformed',
        'timeout-or-duplicate' => 'The response is no longer valid: either is too old or has been used previously'
    ]
];
