<?php

if(! function_exists('recaptcha')) {
    /**
     * Get the reCAPTCHA form HTML.
     * 
     * @param  array  $options
     * @return string
     */
    function recaptcha($options = [])
    {
        return app()->make('recaptcha')->formEmbed($options);
    }
}