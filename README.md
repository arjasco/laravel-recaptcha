# Laravel reCAPTCHA

[![Build Status](https://travis-ci.org/arjasco/laravel-recaptcha.svg?branch=master)](https://travis-ci.org/arjasco/laravel-recaptcha)

This package allows you to easily add reCAPTCHA to your Laravel projects.

## Installation

Install with composer:

    composer require arjasco/laravel-recaptcha

If your Laravel version doesn't support auto discovery, add the service provider to your `app.php` configuration.

```php
'providers' => [
    ...
    Arjasco\LaravelRecpatcha\RecaptchaServiceProvider::class,
],
```

Publish the configuration file to your project.

`php artisan vendor:publish --provider="Arjasco\LaravelRecaptcha\RecaptchaServiceProvider"`

Add your site key and secret to `recaptcha.php`.

```php
 return [
     'sitekey' => env('RECAPTCHA_SITEKEY'),
     'secret' => env('RECAPTCHA_SECRET')
 ]
```

## Usage

### Form embed

Use the helper function `recaptcha()` to embed the HTML within your form.

```html
<form action="/contact" method="POST">
    <input type="text" name="full_name" value=""/>
    <input type="text" name="email" value=""/>
    <textarea type="text" name="message"></textarea>
    <button>Send</button>
    {!! recaptcha() !!}
</form>
```

Alternatively, if you are using Blade, you can use the `@recaptcha` directive

```html
<form action="/contact" method="POST">
    ...
    @recaptcha
</form>
```

You may also pass a load of options to the function to further customise the embed.

```html
<form action="/contact" method="POST">
    ...
    {!! recaptcha(['theme' => 'dark', 'size' => 'compact']) !!}
    <!-- Or using the blade directive.. -->
    @recaptcha(['theme' => 'dark', 'size' => 'compact'])
</form>
```

See [here](https://developers.google.com/recaptcha/docs/display) for a table of more options. Omit the `data-` part of each options when using in the options array.

### Verification

Add the `recaptcha` rule to your validator on the request you wish to verify.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $this->validate($request, [
            'g-recaptcha-response' => 'recaptcha'
        ]);
    }
}
```

### Errors

Any errors from the verification will be added to the `recaptcha` key. For example if you wanted to get just the first error you might do something like the following:

```php
<div class="form__errors>
    {!! $errors->first('recaptcha', '<span>:message</span>'); !!}
</div>
```

### Automatic script injection

If you want to automatically inject the script into your HTML head tag, simply add the middleware to any GET route, this is optional and you might wish to include the script yourself.

Register the middleware in your `Kernel.php` file.
```php
<?php

 /**
  * The application's route middleware.
  *
  * These middleware may be assigned to groups or used individually.
  *
  * @var array
  */
 protected $routeMiddleware = [
     ...
     'recaptcha' => \Arjasco\LaravelRecaptcha\RecaptchaMiddleware::class,
 ];
```

Use the middleware on your GET route.
```php
Route::get('/contact', 'ContactController@index')->middleware('recaptcha');
```
