<?php

use Arjasco\LaravelRecaptcha\RecaptchaValidator;
use Arjasco\LaravelRecaptcha\Recaptcha;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Mockery as m;

class RecaptchaValidatorTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testThatWeCanGetMessageRepresentationsOfErrorsWhenAnErrorOccurs()
    {
        $recpatchaValidator = new RecaptchaValidator($recaptcha = m::mock(Recaptcha::class));
        $validator = m::mock(Validator::class);

        $recaptcha->shouldReceive('verify')
                    ->with('post_value')
                    ->once()
                    ->andReturn([
                        'success' => false,
                        'error-codes' => ['missing-input-secret', 'invalid-input-secret']
                    ]);

        $validator->shouldReceive('errors->add')->with('recaptcha',
            'The secret parameter is missing.'
        )->once();

        $validator->shouldReceive('errors->add')->with('recaptcha',
            'The secret parameter is invalid or malformed.'
        )->once();

        $result = $recpatchaValidator->validate(
            'recaptcha', 'post_value', [], $validator
        );

        $this->assertFalse($result);
    }

    public function testThatWeHaveNoErrorsAndReturnTrueWhenSuccess()
    {
        $recpatchaValidator = new RecaptchaValidator($recaptcha = m::mock(Recaptcha::class));
        $validator = m::mock(Validator::class);

        $recaptcha->shouldReceive('verify')
                    ->with('post_value')
                    ->once()
                    ->andReturn([
                        'success' => true,
                        'error-codes' => []
                    ]);

        $validator->shouldNotReceive('errors->add');

        $result = $recpatchaValidator->validate(
            'recaptcha', 'post_value', [], $validator
        );

        $this->assertTrue($result);
    }
}
