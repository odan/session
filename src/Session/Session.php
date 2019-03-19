<?php

namespace Odan\Session;

use RuntimeException;

/**
 * Session handler.
 */
final class Session implements SessionInterface
{
    /**
     * @var SessionInterface
     */
    private $adapter;

    /**
     * Constructor.
     *
     * @param SessionInterface $adapter
     */
    public function __construct(SessionInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Starts the session - do not use session_start().
     *
     * @return bool True if session started
     */
    public function start(): bool
    {
        return $this->adapter->start();
    }

    /**
     * Checks if the session was started.
     *
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->adapter->isStarted();
    }

    /**
     * Migrates the current session to a new session id while maintaining all session attributes.
     *
     * Regenerates the session ID - do not use session_regenerate_id(). This method can optionally
     * change the lifetime of the new cookie that will be emitted by calling this method.
     *
     * @return bool True if session migrated, false if error
     */
    public function regenerateId(): bool
    {
        return $this->adapter->regenerateId();
    }

    /**
     * Clears all session data and regenerates session ID.
     *
     * Do not use session_destroy().
     *
     * Invalidates the current session.
     *
     * Clears all session attributes and flashes and regenerates the session and deletes the old session from persistence.
     *
     * @return bool True if session invalidated, false if error
     */
    public function destroy(): bool
    {
        return $this->adapter->destroy();
    }

    /**
     * Returns the session ID.
     *
     * @return string The session ID
     */
    public function getId(): string
    {
        return $this->adapter->getId();
    }

    /**
     * Sets the session ID.
     *
     * @param string $id
     *
     * @return void
     */
    public function setId(string $id): void
    {
        if ($this->isStarted()) {
            throw new RuntimeException('Cannot change session id when session is active');
        }

        $this->adapter->setId($id);
    }

    /**
     * Returns the session name.
     *
     * @return string The session name
     */
    public function getName(): string
    {
        return $this->adapter->getName();
    }

    /**
     * Sets the session name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->adapter->setName($name);
    }

    /**
     * Returns true if the attribute exists.
     *
     * @param string $name
     *
     * @return bool true if the attribute is defined, false otherwise
     */
    public function has(string $name): bool
    {
        return $this->adapter->has($name);
    }

    /**
     * Gets an attribute by key.
     *
     * @param string $key The key of the element to retrieve
     *
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->adapter->get($key);
    }

    /**
     * Gets an attribute by key.
     *
     * @return mixed|null
     */
    public function all()
    {
        return $this->adapter->all();
    }

    /**
     * Sets an attribute by key.
     *
     * @param string $key The key of the element to set
     * @param mixed $value The data to set
     *
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->adapter->set($key, $value);
    }

    /**
     * Sets multiple attributes at once: takes a keyed array and sets each key => value pair.
     *
     * @param array $values
     *
     * @return void
     */
    public function replace(array $values): void
    {
        $this->adapter->replace($values);
    }

    /**
     * Deletes an attribute by key.
     *
     * @param string $key The key to remove
     *
     * @return void
     */
    public function remove(string $key): void
    {
        $this->adapter->remove($key);
    }

    /**
     * Clear all attributes.
     */
    public function clear(): void
    {
        $this->adapter->clear();
    }

    /**
     * Returns the number of attributes.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->adapter->count();
    }

    /**
     * Force the session to be saved and closed.
     *
     * This method is generally not required for real sessions as the session
     * will be automatically saved at the end of code execution.
     *
     * @return void
     */
    public function save(): void
    {
        $this->adapter->save();
    }

    /**
     * Set session runtime options.
     *
     * @param array $config
     *
     * @return void
     *
     * @see http://php.net/manual/en/session.configuration.php
     */
    public function setOptions(array $config): void
    {
        $this->adapter->setOptions($config);
    }

    /**
     * Get session runtime options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->adapter->getOptions();
    }

    /**
     * Set cookie parameters.
     *
     * @see http://php.net/manual/en/function.session-set-cookie-params.php
     *
     * @param int $lifetime the lifetime of the cookie in seconds
     * @param string|null $path the path where information is stored
     * @param string|null $domain the domain of the cookie
     * @param bool $secure the cookie should only be sent over secure connections
     * @param bool $httpOnly the cookie can only be accessed through the HTTP protocol
     *
     * @return void
     */
    public function setCookieParams(int $lifetime, string $path = null, string $domain = null, bool $secure = false, bool $httpOnly = false): void
    {
        $this->adapter->setCookieParams($lifetime, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Get cookie parameters.
     *
     * @see http://php.net/manual/en/function.session-get-cookie-params.php
     *
     * @return array
     */
    public function getCookieParams(): array
    {
        return $this->adapter->getCookieParams();
    }
}
