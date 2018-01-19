<?php

namespace Odan\Slim\Session;

use Odan\Slim\Session\Adapter\PhpSessionAdapter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Session middleware
 */
final class SessionMiddleware
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (!empty($options['adapter'])) {
            $adapter = $options['adapter'];
            unset($options['adapter']);
        } else {
            $adapter = new PhpSessionAdapter();
        }
        $this->session = new Session($adapter);
        $this->session->setOptions($options);

        $lifetime = $options['cookie_lifetime'];
        $path = $options['cookie_path'];
        $domain = $options['cookie_domain'];
        $secure = (bool)$options['cookie_secure'];
        $httpOnly = (bool)$options['cookie_httponly'];

        $this->session->setCookieParams($lifetime, $path, $domain, $secure, $httpOnly);
        $this->session->setName($options['name']);
    }

    /**
     * Called when middleware needs to be executed.
     *
     * @param RequestInterface $request PSR7 request
     * @param ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $this->session->start();
        $response = $next($request, $response);
        $this->session->save();
        return $response;
    }
}
