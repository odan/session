<?php

namespace Odan\Session;

/**
 * The session data operations.
 */
interface SessionInterface
{
    /**
     * Gets an attribute by key.
     *
     * @param string $key The key name or null to get all values
     * @param mixed $default The default value
     *
     * @return mixed The value. Returns null if the key is not found
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Gets all values as array.
     *
     * @return array<string, mixed> The session values
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
    public function set(string $key, mixed $value): void;

    /**
     * Sets multiple attributes at once: takes a keyed array and sets each key => value pair.
     *
     * @param array<string, mixed> $values The new values
     */
    public function setValues(array $values): void;

    /**
     * Check if an attribute key exists.
     *
     * @param string $key The key
     *
     * @return bool True if the key is set or not
     */
    public function has(string $key): bool;

    /**
     * Deletes an attribute by key.
     *
     * @param string $key The key to remove
     */
    public function delete(string $key): void;

    /**
     * Clear all attributes.
     */
    public function clear(): void;

    /**
     * Get flash handler.
     *
     * @return FlashInterface The flash handler
     */
    public function getFlash(): FlashInterface;
}
