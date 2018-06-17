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
        if ($request->post()) {
            return $this->handleValidation($request, $next);
        } else {
            return $this->handleEmbedding($request, $next);
        }
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

    /**
     * Validate reCAPTCHA request.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure $next
     * @return mixed
     */
    protected function handleValidation($request, Closure $next)
    {
        $response = $this->recaptcha->verify(
            $request->input('g-recaptcha-response')
        );

        if (! $response['success']) {
            $messages = $this->recaptcha->mapErrorsToMessages(
                $response['error-codes']
            );

            return redirect()->back()->with(
                'recaptcha', $messages
            );
        }

        return $next($request);
    }
}
