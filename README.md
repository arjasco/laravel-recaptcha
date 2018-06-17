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

You can use the single provided middleware to automatically inject the reCAPTCHA script and do the verification check. 

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

If you want to automatically inject the script into your HTML head tag, simply add the middleware to any GET route, this is optional and you might wish to include the script yourself.

```php
Route::get('/contact', 'ContactController@index')->middleware('recaptcha');
```

Next apply the same middleware to the POST route of your form, this will verify the reCAPTCHA response when the form is submitted.

```php
Route::post('/contact', 'ContactController@send')->middleware('recaptcha');
```

On a failed response it will send a redirect back to the previous page with an array of any errors. This accessible via the `recaptcha` session key.  

You might do:
```html
@if(session->has('recaptcha'))
<ul>
    @foreach(session()->get('recaptcha') as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
@endif
```
This will return the readable errors has set out on the reCAPTCHA documentation.

You may want to simply check for the existence of the `recaptcha` session key to present a more generic error to the user. 

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

You may also pass a load of options to the function to further customise the embed.

```html
<form action="/contact" method="POST">
    ...
    {!! recaptcha(['theme' => 'dark', 'size' => 'compact']) !!}
</form>
```

See [here](https://developers.google.com/recaptcha/docs/display) for a table of more options. Omit the `data-` part of each options when using in the options array.