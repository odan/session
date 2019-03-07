<?php

namespace Odan\Session\Adapter;

use RuntimeException;

/**
 * A memory (array) session handler adapter.
 */
class MemorySessionAdapter implements SessionAdapterInterface
{
    private $data = [];

    private $id = '';

    private $started = false;

    private $name = '';

    private $config = [];

    private $cookie = [];

    /**
     * Constructor.
     */
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateId(): bool
    {
        $this->id = str_replace('.', '', uniqid('sess_', true));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): bool
    {
        $this->data = [];
        $this->regenerateId();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        if ($this->isStarted()) {
            throw new RuntimeException('Cannot change session name when session is active');
        }
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        if (empty($this->data)) {
            return false;
        }

        return array_key_exists($name, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        if (!$this->has($name)) {
            return $default;
        }

        return $this->data[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $values): void
    {
        $this->data = array_replace_recursive($this->data, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {
        unset($this->data[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function save(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $config): void
    {
        foreach ($config as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getCookieParams(): array
    {
        return $this->cookie;
    }
}
