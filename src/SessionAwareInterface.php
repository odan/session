<?php

namespace Odan\Session;

interface SessionAwareInterface
{
    public function setSession(SessionInterface $session);
}
