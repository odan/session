<?php

namespace Odan\Session;

use ArrayObject;

/**
 * Flash messages.
 */
final class Flash implements FlashInterface
{
    /**
     * Message storage.
     *
     * @var ArrayObject
     */
    private $storage;

    /**
     * Message storage key.
     *
     * @var string
     */
    private $storageKey;

    /**
     * The constructor.
     *
     * @param ArrayObject $storage The storage
     * @param string $storageKey The flash storage key
     */
    public function __construct(ArrayObject $storage, string $storageKey = '_flash')
    {
        $this->storage = $storage;
        $this->storageKey = $storageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $key, string $message): void
    {
        // Create array for this key
        if (!isset($this->storage[$this->storageKey][$key])) {
            $this->storage[$this->storageKey][$key] = [];
        }

        // Push onto the array
        $this->storage[$this->storageKey][$key][] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key): array
    {
        if (!$this->has($key)) {
            return [];
        }

        $return = $this->storage[$this->storageKey][$key];
        unset($this->storage[$this->storageKey][$key]);

        return (array)$return;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return isset($this->storage[$this->storageKey][$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        if ($this->storage->offsetExists($this->storageKey)) {
            $this->storage->offsetUnset($this->storageKey);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, array $messages): void
    {
        $this->storage[$this->storageKey][$key] = $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        $result = $this->storage[$this->storageKey] ?? [];
        $this->clear();

        return (array)$result;
    }
}
