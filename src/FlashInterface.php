<?php

namespace Odan\Session;

/**
 * Flash messages.
 */
interface FlashInterface
{
    /**
     * Add flash message.
     *
     * @param string $key The key to store the message under
     * @param string $message Message to show on next request
     *
     * @return void
     */
    public function add(string $key, string $message): void;

    /**
     * Get flash messages.
     *
     * @param string $key The key to get the message from
     *
     * @return array The messages
     */
    public function get(string $key): array;

    /**
     * Has flash message.
     *
     * @param string $key The key to get the message from
     *
     * @return bool Whether the message is set or not
     */
    public function has(string $key): bool;

    /**
     * Clear all messages.
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Set all messages.
     *
     * @param string $key The key to clear
     * @param array<int, string> $messages The messages
     *
     * @return void
     */
    public function set(string $key, array $messages): void;

    /**
     * Gets all flash messages.
     *
     * @return array All messages. Can be an empty array.
     */
    public function all(): array;
}
