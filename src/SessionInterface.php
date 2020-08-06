<?php

namespace Odan\Session;

use Odan\Session\Exception\SessionException;

/**
 * Interface.
 */
interface SessionInterface
{
    /**
     * Starts the session - do not use session_start().
     */
    public function start(): void;

    /**
     * Get flash handler.
     *
     * @return FlashInterface The flash handler
     */
    public function getFlash(): FlashInterface;

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
     * Sets the session ID.
     *
     * @param string $id The session id
     *
     * @throws SessionException On error
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
     * @param string $name The session name
     *
     * @throws SessionException Cannot change session name when session is active
     */
    public function setName(string $name): void;

    /**
     * Returns true if the key exists.
     *
     * @param string $key The key
     *
     * @return bool true if the key is defined, false otherwise
     */
    public function has(string $key): bool;

    /**
     * Gets an attribute by key.
     *
     * @param string $key The key name or null to get all values
     *
     * @return mixed|null Should return null if the key is not found
     */
    public function get(string $key);

    /**
     * Gets all values as array.
     *
     * @return array The session values
     */
    public function all(): array;

    /**
     * Sets an attribute by key.
     *
     * @param string $key The key of the element to set
     * @param mixed $value The data to set
     *
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Sets multiple attributes at once: takes a keyed array and sets each key => value pair.
     *
     * @param array $attributes The new attributes
     */
    public function replace(array $attributes): void;

    /**
     * Deletes an attribute by key.
     *
     * @param string $key The key to remove
     */
    public function remove(string $key): void;

    /**
     * Clear all attributes.
     */
    public function clear(): void;

    /**
     * Returns the number of attributes.
     *
     * @return int The number of keys
     */
    public function count(): int;

    /**
     * Force the session to be saved and closed.
     *
     * This method is generally not required for real sessions as the session
     * will be automatically saved at the end of code execution.
     *
     * @throws SessionException On error
     */
    public function save(): void;

    /**
     * Set session runtime configuration.
     *
     * @see http://php.net/manual/en/session.configuration.php
     *
     * @param array $config The session options
     */
    public function setOptions(array $config): void;

    /**
     * Get session runtime configuration.
     *
     * @return array The options
     */
    public function getOptions(): array;

    /**
     * Set cookie parameters.
     *
     * @see http://php.net/manual/en/function.session-set-cookie-params.php
     *
     * @param int $lifetime The lifetime of the cookie in seconds
     * @param string|null $path The path where information is stored
     * @param string|null $domain The domain of the cookie
     * @param bool $secure The cookie should only be sent over secure connections
     * @param bool $httpOnly The cookie can only be accessed through the HTTP protocol
     */
    public function setCookieParams(
        int $lifetime,
        string $path = null,
        string $domain = null,
        bool $secure = false,
        bool $httpOnly = false
    ): void;

    /**
     * Get cookie parameters.
     *
     * @see http://php.net/manual/en/function.session-get-cookie-params.php
     *
     * @return array The cookie parameters
     */
    public function getCookieParams(): array;
}
