<?php

namespace Odan\Session;

use RuntimeException;

/**
 * A memory (array) session handler adapter.
 */
final class MemorySession implements SessionInterface
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
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
        if ($this->isStarted()) {
            throw new RuntimeException('Cannot change session id when session is active');
        }

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
    public function has(string $key): bool
    {
        if (empty($this->data)) {
            return false;
        }

        return array_key_exists($key, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
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
    public function remove(string $key): void
    {
        unset($this->data[$key]);
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
    public function setCookieParams(
        int $lifetime,
        string $path = null,
        string $domain = null,
        bool $secure = false,
        bool $httpOnly = false
    ): void {
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
