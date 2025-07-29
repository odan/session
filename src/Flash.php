<?php

namespace Odan\Session;

use ArrayAccess;

/**
 * Flash messages.
 */
final class Flash implements FlashInterface
{
    /**
     * @var array<string, mixed>|ArrayAccess<string, mixed>
     */
    private array|ArrayAccess $storage;

    private string $storageKey;

    /**
     * @param array<string, mixed>|ArrayAccess<string, mixed> $storage
     */
    public function __construct(array|ArrayAccess &$storage, string $storageKey = '_flash')
    {
        $this->storage = &$storage;
        $this->storageKey = $storageKey;
    }

    public function add(string $key, string $message): void
    {
        // Create array for this key
        if (!isset($this->storage[$this->storageKey][$key])) {
            $this->storage[$this->storageKey][$key] = [];
        }

        // Push onto the array
        $this->storage[$this->storageKey][$key][] = $message;
    }

    public function get(string $key): array
    {
        if (!$this->has($key)) {
            return [];
        }

        $return = $this->storage[$this->storageKey][$key];
        unset($this->storage[$this->storageKey][$key]);

        return (array)$return;
    }

    public function has(string $key): bool
    {
        return isset($this->storage[$this->storageKey][$key]);
    }

    public function clear(): void
    {
        unset($this->storage[$this->storageKey]);
    }

    public function set(string $key, array $messages): void
    {
        $this->storage[$this->storageKey][$key] = $messages;
    }

    public function all(): array
    {
        $result = $this->storage[$this->storageKey] ?? [];
        $this->clear();

        return (array)$result;
    }
}
