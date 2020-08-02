<?php

namespace Odan\Session;

use ArrayObject;
use RuntimeException;

/**
 * A PHP Session handler adapter.
 */
final class PhpSession implements SessionInterface
{
    /**
     * @var ArrayObject
     */
    private $storage;

    /**
     * @var FlashInterface
     */
    private $flash;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->storage = new ArrayObject();
        $this->flash = new Flash($this->storage);
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
    public function start(): bool
    {
        $result = session_start();

        // Restore session from request
        $this->storage->exchangeArray($_SESSION ?? []);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateId(): bool
    {
        $this->clear();

        return session_regenerate_id(true);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): bool
    {
        $this->clear();
        $this->save();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                $this->getName(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        if ($this->isStarted()) {
            session_destroy();
            session_unset();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return session_id() ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
        if ($this->isStarted()) {
            throw new RuntimeException('Cannot change session id when session is active');
        }

        session_id($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return session_name();
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): void
    {
        if ($this->isStarted()) {
            throw new RuntimeException('Cannot change session name when session is active');
        }
        session_name($name);
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
        return $this->has($key) ? $this->storage->offsetGet($key) : null;
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
        $this->storage->offsetSet($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $values): void
    {
        $this->storage->exchangeArray(
            array_replace_recursive($this->storage->getArrayCopy(), $values)
        );
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
        $_SESSION = (array)$this->storage;
        session_write_close();
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $config): void
    {
        foreach ($config as $key => $value) {
            ini_set('session.' . $key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        $config = [];

        foreach (ini_get_all('session') as $key => $value) {
            $config[substr($key, 8)] = $value['local_value'];
        }

        return $config;
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
        session_set_cookie_params($lifetime, $path ?? '/', $domain, $secure, $httpOnly);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams(): array
    {
        return session_get_cookie_params();
    }
}
