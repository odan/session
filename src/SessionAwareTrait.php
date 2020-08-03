<?php

namespace Odan\Session;

trait SessionAwareTrait
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Sets session.
     *
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }
}
