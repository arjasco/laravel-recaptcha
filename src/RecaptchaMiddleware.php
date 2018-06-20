<?php

namespace Arjasco\LaravelRecaptcha;

use Closure;

class RecaptchaMiddleware
{
    /**
     * Recpatcha instance.
     *
     * @var \Arjasco\LaravelRecaptcha\Recaptcha
     */
    protected $recaptcha;

    /**
     * Create a new middleware instance.
     *
     * @param \Arjasco\LaravelRecaptcha\Recaptcha $recaptcha
     */
    public function __construct(Recaptcha $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $this->handleEmbedding($request, $next);
    }

    /**
     * Embed reCAPTCHA javascript.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure $next
     * @return mixed
     */
    protected function handleEmbedding($request, Closure $next)
    {
        $response = $next($request);

        $content = $this->recaptcha->addScriptTagToHead(
            $response->getContent()
        );

        $response->setContent($content);

        return $response;
    }
}
