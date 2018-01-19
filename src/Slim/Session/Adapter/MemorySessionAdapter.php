<?php

namespace Odan\Slim\Session\Adapter;

use RuntimeException;

/**
 * A memory (array) session handler adapter
 */
class MemorySessionAdapter implements SessionAdapterInterface
{
    private $data = [];

    private $id = '';

    private $started = false;

    private $name = '';

    private $config = [];

    private $cookie = [];

    public function __construct()
    {
        $this->setCookieParams(0, '/', '', false, true);

        $config = [];
        foreach (ini_get_all('session') as $key => $value) {
            $config[substr($key, 8)] = $value['local_value'];
        }

        $this->setOptions($config);
    }

    /**
     * {@inheritDoc}
     */
    public function start(): bool
    {
        if (!$this->id) {
            $this->regenerateId();
        }

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
        $this->id = str_replace('.', '', uniqid('sess_', true));
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
        if ($this->isStarted()) {
            throw new RuntimeException('Cannot change session name when session is active');
        }
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

    /**
     * {@inheritDoc}
     */
    public function setOptions(array $config): void
    {
        foreach ($config as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): array
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function setCookieParams(int $lifetime, string $path = null, string $domain = null, bool $secure = false, bool $httpOnly = false): void
    {
        $this->cookie = [
            'lifetime' => $lifetime,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httpOnly,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCookieParams(): array
    {
        return $this->cookie;
    }
}
