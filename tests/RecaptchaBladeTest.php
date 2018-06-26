<?php

use Orchestra\Testbench\TestCase;
use Arjasco\LaravelRecaptcha\RecaptchaServiceProvider;

class RecpatchaBladeTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('recaptcha.sitekey', 'test');
    }

    protected function getPackageProviders($app)
    {
        return [RecaptchaServiceProvider::class];
    }

    public function testThatWeCanUseTheBladeDirectiveWithoutOptions()
    {
        $directive = '@recaptcha';
        $expected = '<?php echo recaptcha(); ?>';

        $result = $this->app->make('blade.compiler')->compileString($directive);

        $this->assertEquals($expected, $result);
    }

    public function testThatWeCanUseTheBladeDirectiveWithOptions()
    {
        $directive = '@recaptcha([\'theme\' => \'dark\'])';
        $expected = '<?php echo recaptcha([\'theme\' => \'dark\']); ?>';

        $result = $this->app->make('blade.compiler')->compileString($directive);

        $this->assertEquals($expected, $result);
    }
}
