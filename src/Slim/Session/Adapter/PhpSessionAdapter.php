<?php

namespace Odan\Slim\Session\Adapter;

/**
 * A PHP Session handler adapter
 */
class PhpSessionAdapter implements SessionAdapterInterface
{
    /**
     * {@inheritDoc}
     */
    public function start(): bool
    {
        return session_start();
    }

    /**
     * {@inheritDoc}
     */
    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * {@inheritDoc}
     */
    public function regenerateId(): bool
    {
        return session_regenerate_id(true);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(): bool
    {
        if ($this->getId()) {
            return true;
        }

        session_unset();
        session_destroy();
        session_write_close();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                $this->getName(),
                '',
                time() - 4200,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return session_id() ?: '';
    }

    /**
     * {@inheritDoc}
     */
    public function setId(string $id): void
    {
        session_id($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return session_name();
    }

    /**
     * {@inheritDoc}
     */
    public function setName(string $name): void
    {
        session_name($name);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        if (empty($_SESSION)) {
            return false;
        }

        return array_key_exists($name, $_SESSION);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->has($name)
            ? $_SESSION[$name]
            : $default;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function replace(array $values): void
    {
        $_SESSION = array_replace_recursive($_SESSION, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $name): void
    {
        unset($_SESSION[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($_SESSION);
    }

    /**
     * {@inheritDoc}
     */
    public function save(): void
    {
        session_write_close();
    }
}
