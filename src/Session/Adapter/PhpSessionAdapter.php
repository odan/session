<?php

namespace Odan\Session\Adapter;

use RuntimeException;

/**
 * A PHP Session handler adapter.
 */
class PhpSessionAdapter implements SessionAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function start(): bool
    {
        return session_start();
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
        return session_regenerate_id(true);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): bool
    {
        $this->clear();

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
        session_write_close();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?string
    {
        return session_id() ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
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
    public function has(string $name): bool
    {
        if (empty($_SESSION)) {
            return false;
        }

        return array_key_exists($name, $_SESSION);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->has($name)
            ? $_SESSION[$name]
            : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $values): void
    {
        $_SESSION = array_replace_recursive($_SESSION, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {
        unset($_SESSION[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($_SESSION);
    }

    /**
     * {@inheritdoc}
     */
    public function save(): void
    {
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
    public function setCookieParams(int $lifetime, string $path = null, string $domain = null, bool $secure = false, bool $httpOnly = false): void
    {
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httpOnly);
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams(): array
    {
        return session_get_cookie_params();
    }
}
