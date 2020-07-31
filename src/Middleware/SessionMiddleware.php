<?php

namespace Odan\Session\Middleware;

use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A PSR-15 Session Middleware.
 */
final class SessionMiddleware implements MiddlewareInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * Constructor.
     *
     * @param SessionInterface $session The session handler
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        $response = $handler->handle($request);
        $this->session->save();

        return $response;
    }
}
