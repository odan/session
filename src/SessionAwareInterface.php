<?php

namespace Odan\Session;

/**
 * Describes a session-aware instance.
 */
interface SessionAwareInterface
{
    /**
     * @param SessionInterface $session The session
     *
     * @return void
     */
    public function setSession(SessionInterface $session): void;
}
