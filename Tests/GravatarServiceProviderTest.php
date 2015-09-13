<?php

namespace EmanueleMinotto\GravatarServiceProvider\Tests;

use EmanueleMinotto\GravatarServiceProvider\GravatarServiceProvider;
use PHPUnit_Framework_TestCase;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Emanuele Minotto <minottoemanuele@gmail.com>
 *
 * @coversDefaultClass \EmanueleMinotto\GravatarServiceProvider\GravatarServiceProvider
 */
class GravatarServiceProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test service registration.
     * 
     * @covers ::boot
     * @covers ::register
     *
     * @return void
     */
    public function testRegisterServiceProvider()
    {
        $app = new Application();
        $app->register(new GravatarServiceProvider());
        $app->boot();

        $this->assertInstanceOf('EmanueleMinotto\\Gravatar\\Client', $app['gravatar']);
    }

    /**
     * Test Twig extension integration in Silex (enabled, default).
     * 
     * @coversNothing
     *
     * @return void
     */
    public function testTwigExtension()
    {
        $app = new Application();
        $app->register(new TwigServiceProvider());
        $app->register(new GravatarServiceProvider());
        $app->boot();

        $this->assertTrue($app['twig']->hasExtension('emanueleminotto_gravatar_twigextension'));
    }

    /**
     * Test Twig extension integration in Silex (disabled).
     * 
     * @coversNothing
     *
     * @return void
     */
    public function testMissingTwigExtension()
    {
        $app = new Application();
        $app->register(new TwigServiceProvider());
        $app->register(new GravatarServiceProvider(), [
            'gravatar.twig' => false,
        ]);
        $app->boot();

        $this->assertFalse($app['twig']->hasExtension('emanueleminotto_gravatar_twigextension'));
    }

    /**
     * Simulates a request and controls the output.
     * 
     * @param string $email   User email.
     * @param string $format  Request format (default json).
     * @param array  $options Request options.
     *
     * @dataProvider requestDataProvider
     *
     * @return void
     */
    public function testRequest($email, $format = 'json', array $options = [])
    {
        $app = new Application();
        $app->register(new GravatarServiceProvider());

        $app->get('/', function () use ($app, $email, $format, $options) {
            return $app['gravatar']->getProfileUrl($email, $format, $options);
        });

        $request = Request::create('/');
        $response = $app->handle($request);

        $regexp = '/^.+\/[a-zA-Z0-9]{32,32}\.(json|php|vcf|xml|qr)(\?.+)?/';
        $this->assertRegExp($regexp, $response->getContent());
        $this->assertNotFalse(filter_var($response->getContent(), FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED));
        $this->assertStringStartsWith('https://www.gravatar.com/', $response->getContent());
    }

    /**
     * Example profiles.
     * 
     * @return array
     */
    public function requestDataProvider()
    {
        return [
            ['beau.lebens@gmail.com', 'json', ['callback' => 'test']],
            ['beau.lebens@gmail.com', 'json'],
            ['beau.lebens@gmail.com', 'php'],
            ['beau.lebens@gmail.com', 'qr', ['s' => 150]],
            ['beau.lebens@gmail.com', 'qr'],
            ['beau.lebens@gmail.com', 'vcf'],
            ['beau.lebens@gmail.com', 'xml'],
            ['beau.lebens@gmail.com'],
        ];
    }
}
