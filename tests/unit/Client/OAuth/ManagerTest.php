<?php
/**
 * @author @ct-jensschulze <jens.schulze@commercetools.de>
 * @created: 29.01.15, 14:16
 */

namespace Sphere\Core\Client\OAuth;


use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\BufferStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Subscriber\Mock;
use Sphere\Core\Config;

class ManagerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!function_exists('apc_store')) {
            $this->markTestSkipped(
                'The APCU extension is not available.'
            );
        }
    }

    protected function getConfig()
    {
        $config = new Config();
        $config->fromArray([
                Config::CLIENT_ID => 'id',
                Config::CLIENT_SECRET => 'secret',
                Config::OAUTH_URL => 'oauthUrl',
                Config::PROJECT => 'project',
                Config::API_URL => 'apiUrl'
        ]);
        return $config;
    }

    /**
     * @return Manager
     */
    protected function getManager($config, $returnValue, $statusCode = 200)
    {
        $manager = new Manager($config);

        if (is_array($returnValue)) {
            $returnValue = json_encode($returnValue);
        }

        if (version_compare(HttpClient::VERSION, '6.0.0', '>=')) {
            $mockBody = new BufferStream();
            $mockBody->write($returnValue);

            $mock = new MockHandler([
                new Response($statusCode, [], $mockBody)
            ]);

            $handler = HandlerStack::create($mock);
            // Add the mock subscriber to the client.
            $manager->getHttpClient(['handler' => $mock]);
        } else {
            $mockBody = new \GuzzleHttp\Stream\BufferStream();
            $mockBody->write($returnValue);

            $mock = new Mock([
                new \GuzzleHttp\Message\Response($statusCode, [], $mockBody)
            ]);
            // Add the mock subscriber to the client.
            $manager->getHttpClient()->getEmitter()->attach($mock);
        }

        return $manager;
    }

    public function testToken()
    {
        $manager = $this->getManager(
            $this->getConfig(),
            [
                "access_token" => "myToken",
                "token_type" => "Bearer",
                "expires_in" => 1000,
                "scope" => "manage_project:project"
            ]
        );
        $this->assertInstanceOf('\Sphere\Core\Client\OAuth\Token', $manager->getToken());
    }

    public function testCache()
    {
        $manager = $this->getManager(
            $this->getConfig(),
            [
                "access_token" => "myToken",
                "token_type" => "Bearer",
                "expires_in" => 1000,
                "scope" => "manage_project:project"
            ]
        );
        $manager->getToken(); // first call ensures caching of token
        $this->assertEmpty($manager->getToken()->getTtl()); // ttl should be empty as token comes from cache
    }

    /**
     * @expectedException \Sphere\Core\Client\OAuth\AuthorizeException
     */
    public function testError()
    {
        $manager = $this->getMock(
            '\Sphere\Core\Client\OAuth\Manager',
            ['execute', 'getCacheToken'],
            [$this->getConfig()]
        );
        $manager->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(
                new Response(
                    401,
                    [],
                    json_encode([
                        'error' => 'invalid_client',
                        'error_description' =>
                            'Please provide valid client credentials using HTTP Basic Authentication.'
                    ])
                )
            ));
        $manager->expects($this->any())
            ->method('getCacheToken')
            ->will($this->returnValue(false));

        /**
         * @var Manager $manager
         */
        $manager->getToken();
    }

    public function testOAuthUrl()
    {
        $manager = $this->getManager($this->getConfig(), []);

        // change visibility of getBaseUrl
        $class = new \ReflectionClass($manager);
        $method = $class->getMethod('getBaseUrl');
        $method->setAccessible(true);
        $output = $method->invoke($manager);

        $this->assertSame($this->getConfig()->getOauthUrl(), $output);
    }
}