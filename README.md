# Installation and usage instructions:

## What it does:

This package allows you to integrate CAPTCHA API to your Laravel application.

## Installation:

```php
composer require felipetti/captcha
```

## Usage instructions:

First you publish config file:

```php
php artisan vendor:publish --tag=captcha
```

Config file named captcha.php is published in config folder of root directory. Open the file and put the secret key provided by Google inside "secret_key" field.

```php
/*
    |--------------------------------------------------------------------------
    | Secret Key
    |--------------------------------------------------------------------------
    |
    | This is the CAPTCHA key that only back-end has.
    |
    */

    'secret_key' => ''
```

There are three ways of captcha service integration, assuming $captcha is the field where front-end sends captcha response:

### Form request way:

```php
    use Felipetti\Captcha\Rule\CaptchaVerification;
    
    public function rules()
    {
        $captcha = ['required', 'string', new CaptchaVerification];
        
        return compact('captcha');
    }
```

### Request validation way:

Let's suppose we have a captcha controller, and we want to make validation inside:

```php
use Felipetti\Captcha\Rule\CaptchaVerification;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate(
            ['captcha' => [new CaptchaVerification]]
        );

        //...
    }
}
```

### Validator way:

```php
use Felipetti\Captcha\Rule\CaptchaVerification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function __invoke(Request $request)
    {
        $rules  = ['captcha' => [new CaptchaVerification]];

        Validator::make($request->all(), $rules)->validate();

        //...
    }
}
```

## Config:

Explanations in config file are quite expressive, so if you have any questions, please email [guill388@hotmail.com](mailto:guill388@hotmail.com).

## Comments:

This package is not for captcha V3, only V2. Please star me if you liked the package, it will really help me a lot =).

## Security:

If you discover any security-related issues, please e-mail me to the one above instead of using the issue tracker.

## License:

The MIT License (MIT).
