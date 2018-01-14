<?php

namespace Odan\Slim\Session\Adapter;

/**
 * A memory (array) session handler adapter
 */
class MemorySessionAdapter implements SessionAdapterInterface
{
    private $data = [];

    private $id = '';

    private $started = false;

    private $name = '';

    /**
     * {@inheritDoc}
     */
    public function start(): bool
    {
        $this->started = true;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * {@inheritDoc}
     */
    public function regenerateId(): bool
    {
        $this->id = uniqid('session', true);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(): bool
    {
        $this->data = [];
        $this->regenerateId();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        if (empty($this->data)) {
            return false;
        }

        return array_key_exists($name, $this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name, $default = null)
    {
        if (!$this->has($name)) {
            return $default;
        }

        return $this->data[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function replace(array $values): void
    {
        $this->data = array_replace_recursive($this->data, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $name): void
    {
        unset($this->data[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function save(): void
    {

    }
}