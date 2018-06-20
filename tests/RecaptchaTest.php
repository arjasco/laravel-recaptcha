<?php

use Arjasco\LaravelRecaptcha\Recaptcha;
use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use Mockery as m;

class RecpatchaTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testThatWeCanCanEmbedFormHtml()
    {
        $recaptcha = new Recaptcha(m::mock(Client::class), ['sitekey' => '123']);

        $html = $recaptcha->formEmbed();

        $this->assertSame('<div class="g-recaptcha" data-sitekey="123"></div>', $html);
    }

    public function testThatWeCanCanEmbedFormHtmlWithOptions()
    {
        $recaptcha = new Recaptcha(m::mock(Client::class), ['sitekey' => '123']);

        $html = $recaptcha->formEmbed([
            'theme' => 'light',
            'size' => 'compact',
        ]);

        $expected = '<div class="g-recaptcha" data-sitekey="123" data-theme="light" data-size="compact"></div>';

        $this->assertSame($expected, $html);
    }

    public function testThatWeCanEmbedTheScriptTagToTheHead()
    {
        $original = file_get_contents(__DIR__.'/fixtures/original.html');
        $expected = file_get_contents(__DIR__.'/fixtures/htmlWithScriptTag.html');

        $recaptcha = new Recaptcha(m::mock(Client::class));

        $content = $recaptcha->addScriptTagToHead($original);

        $this->assertSame($expected, $content);
    }

    public function testThatWeAttemptToVerifyTheResponse()
    {
        $mockHandler = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{"success": true}')
        ]);

        $recaptcha = new Recaptcha(
            new Client(['handler' => $mockHandler]),
            ['secret' => 'secret']
        );

        $response = $recaptcha->verify('response');

        $this->assertSame(['success' => true], $response);
    }
}
