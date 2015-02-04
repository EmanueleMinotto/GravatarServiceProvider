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
     * @covers ::boot
     * @covers ::register
     */
    public function testRegisterServiceProvider()
    {
        $app = new Application();
        $app->register(new GravatarServiceProvider());
        $app->boot();

        $this->assertInstanceOf('EmanueleMinotto\\Gravatar\\Client', $app['gravatar']);
    }

    /**
     * @coversNothing
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
     * @coversNothing
     */
    public function testMissingTwigExtension()
    {
        $app = new Application();
        $app->register(new TwigServiceProvider());
        $app->register(new GravatarServiceProvider(), array(
            'gravatar.twig' => false,
        ));
        $app->boot();

        $this->assertFalse($app['twig']->hasExtension('emanueleminotto_gravatar_twigextension'));
    }

    /**
     * Simulates a request and controls the output.
     *
     * @dataProvider requestDataProvider
     */
    public function testRequest($email, $format = 'json', array $options = array())
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
     * @return array
     */
    public function requestDataProvider()
    {
        return array(
            array('beau.lebens@gmail.com', 'json', array('callback' => 'test')),
            array('beau.lebens@gmail.com', 'json'),
            array('beau.lebens@gmail.com', 'php'),
            array('beau.lebens@gmail.com', 'qr', array('s' => 150)),
            array('beau.lebens@gmail.com', 'qr'),
            array('beau.lebens@gmail.com', 'vcf'),
            array('beau.lebens@gmail.com', 'xml'),
            array('beau.lebens@gmail.com'),
        );
    }
}
