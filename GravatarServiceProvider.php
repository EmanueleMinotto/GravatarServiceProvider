<?php

namespace EmanueleMinotto\GravatarServiceProvider;

use EmanueleMinotto\Gravatar\Client;
use EmanueleMinotto\Gravatar\Twig\GravatarExtension;
use GuzzleHttp\ClientInterface as GuzzleHttp_ClientInterface;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * A Gravatar service provider for Silex 1.
 *
 * @author Emanuele Minotto <minottoemanuele@gmail.com>
 *
 * @link http://silex.sensiolabs.org/doc/providers.html#creating-a-provider
 */
class GravatarServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['gravatar'] = $app->share(function ($app) {
            return new Client();
        });
        // instance of a Guzzle HTTP client
        $app['gravatar.http_client'] = null;
        // Twig extension (boolean)
        $app['gravatar.twig'] = true;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        // Guzzle HTTP client
        $httpClient = $app['gravatar.http_client'];
        if (is_object($httpClient) && $httpClient instanceof GuzzleHttp_ClientInterface) {
            $app['gravatar']->setHttpClient($httpClient);
        }

        // Twig extension
        if (isset($app['twig']) && (boolean) $app['gravatar.twig']) {
            $extension = new GravatarExtension($app['gravatar']);
            $app['twig']->addExtension($extension);
        }
    }
}
