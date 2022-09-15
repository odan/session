<?php

namespace Odan\Session;

use Odan\Session\Exception\SessionException;

interface SessionManagerInterface
{
    /**
     * Starts the session - do not use session_start().
     */
    public function start(): void;

    /**
     * Checks if the session was started.
     *
     * @return bool Session status
     */
    public function isStarted(): bool;

    /**
     * Migrates the current session to a new session id while maintaining all session attributes.
     *
     * Regenerates the session ID - do not use session_regenerate_id(). This method can optionally
     * change the lifetime of the new cookie that will be emitted by calling this method.
     *
     * @throws SessionException On error
     */
    public function regenerateId(): void;

    /**
     * Clears all session data and regenerates session ID.
     *
     * Do not use session_destroy().
     *
     * Invalidates the current session.
     *
     * Clears all session attributes and flashes and regenerates the session
     * and deletes the old session from persistence.
     *
     * @throws SessionException On error
     */
    public function destroy(): void;

    /**
     * Returns the session ID.
     *
     * @return string The session ID
     */
    public function getId(): string;

    /**
     * Returns the session name.
     *
     * @return string The session name
     */
    public function getName(): string;

    /**
     * Force the session to be saved and closed.
     *
     * This method is generally not required for real sessions as the session
     * will be automatically saved at the end of code execution.
     *
     * @throws SessionException On error
     */
    public function save(): void;
}
