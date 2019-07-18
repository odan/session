<?php

namespace Odan\Session\Test;

use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;

/**
 * Test.
 *
 * @coversDefaultClass \Odan\Session\SessionMiddleware
 */
class SessionMiddlewareTest extends AbstractTestCase
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var SessionMiddleware
     */
    protected $middleware;

    /** {@inheritdoc} */
    protected function setUp(): void
    {
        $_SESSION = [];

        $this->session = new PhpSession();
        $this->session->setOptions([
            'name' => 'app',
            // turn off automatic sending of cache headers entirely
            'cache_limiter' => '',
            // garbage collection
            'gc_probability' => 1,
            'gc_divisor' => 1,
            'gc_maxlifetime' => 30 * 24 * 60 * 60,
        ]);

        $lifetime = strtotime('20 minutes') - time();
        $this->session->setCookieParams($lifetime, '/', '', false, false);
        $this->session->setName('app');

        $this->middleware = new SessionMiddleware($this->session);
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @covers ::process
     */
    public function testInvoke(): void
    {
        $request = $this->createRequest('GET', '/');

        $callback = static function (ResponseInterface $response) {
            return $response->withHeader('test', 'ok');
        };
        $handler = new MockedRequestHandler($callback);
        $response = $this->middleware->process($request, $handler);

        //session must be closed
        $this->assertFalse($this->session->isStarted());

        // check next callback result
        $this->assertSame('ok', $response->getHeader('test')[0]);
    }

    /**
     * Create a new request.
     *
     * @param string $method the http method
     * @param string $url the url
     *
     * @return Request the request
     */
    protected function createRequest(string $method, string $url): Request
    {
        $env = Environment::mock();
        $uri = Uri::createFromString($url);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new RequestBody();
        $uploadedFiles = (array)UploadedFile::createFromEnvironment($env);

        return new Request($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);
    }
}
