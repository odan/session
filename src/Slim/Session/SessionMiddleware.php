<?php

namespace Odan\Slim\Session;

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
     * @param Session $session The session object
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
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
