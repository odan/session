<?php

namespace Odan\Slim\Session\Adapter;

use RuntimeException;

/**
 * Interface SessionAdapterInterface
 */
interface SessionAdapterInterface
{

    /**
     * Starts the session - do not use session_start().
     *
     * @return bool True if session started
     */
    public function start(): bool;

    /**
     * Checks if the session was started.
     *
     * @return bool
     */
    public function isStarted(): bool;

    /**
     * Migrates the current session to a new session id while maintaining all session attributes.
     *
     * Regenerates the session ID - do not use session_regenerate_id(). This method can optionally
     * change the lifetime of the new cookie that will be emitted by calling this method.
     *
     * @return bool True if session migrated, false if error
     */
    public function regenerateId(): bool;

    /**
     * Clears all session data and regenerates session ID.
     *
     * Do not use session_destroy().
     *
     * Invalidates the current session.
     *
     * Clears all session attributes and flashes and regenerates the session and deletes the old session from persistence.
     *
     * @return bool True if session invalidated, false if error
     */
    public function destroy(): bool;

    /**
     * Returns the session ID.
     *
     * @return string|null The session ID
     */
    public function getId();

    /**
     * Sets the session ID.
     *
     * @param string $id
     * @return void
     */
    public function setId(string $id): void;

    /**
     * Returns the session name.
     *
     * @return string The session name
     */
    public function getName(): string;

    /**
     * Sets the session name.
     *
     * @param string $name
     * @return void
     * @throws RuntimeException Cannot change session name when session is active
     */
    public function setName(string $name): void;

    /**
     * Returns true if the attribute exists.
     *
     * @param string $salt
     * @return bool true if the attribute is defined, false otherwise
     */
    public function has(string $salt): bool;

    /**
     * Gets an attribute by key.
     *
     * @param string $name The attribute name
     * @param mixed|null $default The default value if not found
     * @return mixed|null
     */
    public function get(string $name, $default = null);

    /**
     * Sets an attribute by key.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set(string $name, $value);

    /**
     * Sets multiple attributes at once: takes a keyed array and sets each key => value pair.
     *
     * @param array $attributes
     * @return void
     */
    public function replace(array $attributes): void;

    /**
     * Deletes an attribute by key.
     *
     * @param string $name
     * @return void
     */
    public function remove(string $name): void;

    /**
     * Clear all attributes.
     */
    public function clear(): void;

    /**
     * Returns the number of attributes.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Force the session to be saved and closed.
     *
     * This method is generally not required for real sessions as the session
     * will be automatically saved at the end of code execution.
     *
     * @return void
     */
    public function save(): void;

    /**
     * Set session runtime configuration
     *
     * @param array $config
     * @return void
     * @link http://php.net/manual/en/session.configuration.php
     */
    public function setOptions(array $config): void;

    /**
     * Get session runtime configuration
     *
     * @return array
     */
    public function getOptions(): array;

    /**
     * Set cookie parameters.
     *
     * @link http://php.net/manual/en/function.session-set-cookie-params.php
     *
     * @param int $lifetime The lifetime of the cookie in seconds.
     * @param string $path The path where information is stored.
     * @param string $domain The domain of the cookie.
     * @param bool $secure The cookie should only be sent over secure connections.
     * @param bool $httpOnly The cookie can only be accessed through the HTTP protocol.
     * @return void
     */
    public function setCookieParams(int $lifetime, string $path = null, string $domain = null, bool $secure = false, bool $httpOnly = false): void;

    /**
     * Get cookie parameters.
     *
     * @see http://php.net/manual/en/function.session-get-cookie-params.php
     *
     * @return array
     */
    public function getCookieParams(): array;
}
