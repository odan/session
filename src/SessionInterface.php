<?php

namespace Odan\Session;

use ArrayObject;
use RuntimeException;

/**
 * Interface.
 */
interface SessionInterface
{
    /**
     * Starts the session - do not use session_start().
     *
     * @return bool True if session started
     */
    public function start(): bool;

    /**
     * Get storage.
     *
     * @return ArrayObject The storage
     */
    public function getStorage(): ArrayObject;

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
     * Clears all session attributes and flashes and regenerates the session
     * and deletes the old session from persistence.
     *
     * @return bool True if session invalidated, false if error
     */
    public function destroy(): bool;

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
     * @throws RuntimeException Cannot change session name when session is active
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
     */
    public function all(): array;

    /**
     * Sets an attribute by key.
     *
     * @param string $key The key of the element to set
     * @param mixed $value The data to set
     */
    public function set(string $key, $value): void;

    /**
     * Sets multiple attributes at once: takes a keyed array and sets each key => value pair.
     *
     * @param array $attributes The new atributes
     */
    public function replace(array $attributes): void;

    /**
     * Deletes an attribute by key.
     */
    public function remove(string $key): void;

    /**
     * Clear all attributes.
     */
    public function clear(): void;

    /**
     * Returns the number of attributes.
     */
    public function count(): int;

    /**
     * Force the session to be saved and closed.
     *
     * This method is generally not required for real sessions as the session
     * will be automatically saved at the end of code execution.
     */
    public function save(): void;

    /**
     * Set session runtime configuration.
     *
     * @see http://php.net/manual/en/session.configuration.php
     */
    public function setOptions(array $config): void;

    /**
     * Get session runtime configuration.
     */
    public function getOptions(): array;

    /**
     * Set cookie parameters.
     *
     * @see http://php.net/manual/en/function.session-set-cookie-params.php
     *
     * @param int $lifetime the lifetime of the cookie in seconds
     * @param string|null $path the path where information is stored
     * @param string|null $domain the domain of the cookie
     * @param bool $secure the cookie should only be sent over secure connections
     * @param bool $httpOnly the cookie can only be accessed through the HTTP protocol
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
     */
    public function getCookieParams(): array;
}
