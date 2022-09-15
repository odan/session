<?php

namespace Odan\Session;

/**
 * Interface.
 */
interface SessionInterface
{
    /**
     * Gets an attribute by key.
     *
     * @param string $key The key name or null to get all values
     * @param null $default The default value
     *
     * @return mixed|null Should return null if the key is not found
     */
    public function get(string $key, $default = null);

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
     * @param array $values The new values
     */
    public function setValues(array $values): void;

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
