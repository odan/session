<?php

namespace Odan\Session;

use ArrayObject;
use RuntimeException;

/**
 * A memory (array) session handler adapter.
 */
final class MemorySession implements SessionInterface
{
    /**
     * @var ArrayObject
     */
    private $storage;

    /**
     * @var Flash
     */
    private $flash;

    /**
     * @var string
     */
    private $id = '';

    /**
     * @var bool
     */
    private $started = false;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $cookie = [];

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->storage = new ArrayObject();
        $this->flash = new Flash($this->storage);

        $this->setCookieParams(0, '/', '', false, true);

        $config = [];
        foreach ((array)ini_get_all('session') as $key => $value) {
            $config[substr($key, 8)] = $value['local_value'];
        }

        $this->setOptions($config);
    }

    /**
     * Get storage.
     *
     * @return ArrayObject The storage
     */
    public function getStorage(): ArrayObject
    {
        return $this->storage;
    }

    /**
     * Get flash instance.
     *
     * @return FlashInterface The flash instance
     */
    public function getFlash(): FlashInterface
    {
        return $this->flash;
    }

    /**
     * {@inheritdoc}
     */
    public function start(): void
    {
        if (!$this->id) {
            $this->regenerateId();
        }

        $this->started = true;
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
    public function regenerateId(): void
    {
        $this->id = str_replace('.', '', uniqid('sess_', true));
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        $this->storage->exchangeArray([]);
        $this->regenerateId();
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
        if (empty($this->storage)) {
            return false;
        }

        return $this->storage->offsetExists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        if ($this->has($key)) {
            return $this->storage->offsetGet($key);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return (array)$this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): void
    {
        $this->storage[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $values): void
    {
        $this->storage->exchangeArray(array_replace_recursive($this->storage->getArrayCopy(), $values));
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): void
    {
        $this->storage->offsetUnset($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->storage->exchangeArray([]);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return $this->storage->count();
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
