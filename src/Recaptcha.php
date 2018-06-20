<?php

namespace Arjasco\LaravelRecaptcha;

use GuzzleHttp\Client;

class Recaptcha
{
    /**
     * Http client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * reCAPTCHA options.
     *
     * @var array
     */
    protected $options;

    /**
     * reCAPTCHA verify url.
     *
     * @var string
     */
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Create a new reCAPTCHA instance.
     *
     * @param array $options
     */
    public function __construct(Client $httpClient, $options = [])
    {
        $this->httpClient = $httpClient;
        $this->options = $options;
    }

    /**
     * Add the script tag to a HTML page.
     *
     * @param string $content
     * @return string
     */
    public function addScriptTagToHead($content)
    {
        return preg_replace(
            '/(?<=<head>)(.+)(?=<\/head>)/ims',
            $this->headTagReplacement(),
            $content
        );
    }

    /**
     * Get the reCPATCHA HTML for a form.
     *
     * @param  array  $options
     * @return string
     */
    public function formEmbed($options = [])
    {
        return sprintf(
            '<div class="g-recaptcha"%s%s></div>',
            " data-sitekey=\"{$this->options['sitekey']}\"",
            $this->convertOptionsToDataAttributes($options)
        );
    }

    /**
     * Verify the reCAPTCHA response.
     *
     * @param  string $recaptchaResponse
     * @return array
     */
    public function verify($recaptchaResponse)
    {
        $response = $this->httpClient->post(self::VERIFY_URL, [
            'form_params' => [
                'secret' => $this->options['secret'],
                'response' => $recaptchaResponse
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Convert options to data attribute strings.
     *
     * @param  array $options
     * @return string
     */
    protected function convertOptionsToDataAttributes($options)
    {
        $result = '';

        foreach ($options as $key => $value) {
            $result .= sprintf(' data-%s="%s"', $key, $value);
        }

        return $result;
    }

    /**
     * The RegEx replacement.
     *
     * @return string
     */
    protected function headTagReplacement()
    {
        return "$1<script src=\"https://www.google.com/recaptcha/api.js\" async defer></script>";
    }

}
