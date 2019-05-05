<?php

namespace Odan\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Session Middleware.
 */
class SessionMiddleware
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Invoke middleware.
     *
     * @param  ServerRequestInterface $request The request
     * @param  ResponseInterface $response The response
     * @param  callable $next Next middleware
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        $this->session->start();
        $response = $next($request, $response);
        $this->session->save();

        return $response;
    }
}
