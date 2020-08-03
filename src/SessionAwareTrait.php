<?php

namespace Odan\Session;

/**
 * A LoggerAwareInterface implementation.
 */
trait SessionAwareTrait
{
    /**
     * The session instance.
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * Sets a session.
     *
     * @param SessionInterface $session The session instance
     *
     * @return void
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }
}
