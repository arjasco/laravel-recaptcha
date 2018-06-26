<?php

namespace Arjasco\LaravelRecaptcha;

class RecaptchaValidator
{
    /**
     * Recaptcha instance.
     *
     * @var Recaptcha
     */
    protected $recaptcha;

    /**
     * Create a new Recaptcha validator instance.
     *
     * @param Recaptcha $recaptcha
     */
    public function __construct(Recaptcha $recaptcha)
    {
        $this->recaptcha = $recaptcha;
    }

    /**
     * Validate the attribute.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @param  array $parameters
     * @param  \Illuminate\Validation\Validator $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        $response = $this->recaptcha->verify($value);

        if (! $result = $response['success']) {
            $errors = $this->mapErrorsToMessages($response['error-codes']);

            foreach ($errors as $error) {
                $validator->errors()->add('recaptcha', $error);
            }
        }

        return $result;
    }

    /**
     * Get the corrosponding messages for the given errors.
     *
     * @param  array $errors
     * @return array
     */
    protected function mapErrorsToMessages($errors)
    {
        $mapping = [
            'missing-input-secret' => 'The secret parameter is missing.',
            'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
            'missing-input-response' => 'The response parameter is missing.',
            'invalid-input-response' => 'The response parameter is invalid or malformed.',
            'bad-request' => 'The request is invalid or malformed.',
        ];

        $messages = [];

        foreach ($errors as $error) {
            if (array_key_exists($error, $mapping)) {
                $messages[] = $mapping[$error];
            }
        }

        return $messages;
    }
}
