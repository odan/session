<?php

namespace Odan\Slim\Session;

use Odan\Slim\Session\Adapter\SessionAdapterInterface;

/**
 * Session handler
 */
final class Session
{

    /**
     * @var SessionAdapterInterface
     */
    private $adapter;

    /**
     * Constructor
     *
     * @param SessionAdapterInterface $adapter
     */
    public function __construct(SessionAdapterInterface $adapter)
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
    public function regenerateId()
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
    public function destroy()
    {
        return $this->adapter->destroy();
    }

    /**
     * Returns the session ID.
     *
     * @return string|null The session ID
     */
    public function getId()
    {
        return $this->adapter->getId();
    }

    /**
     * Sets the session ID.
     *
     * @param string $id
     * @return void
     */
    public function setId(string $id)
    {
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
     * @return void
     */
    public function setName(string $name)
    {
        $this->adapter->setName($name);
    }

    /**
     * Returns true if the attribute exists.
     *
     * @param string $name
     * @return bool true if the attribute is defined, false otherwise
     */
    public function has(string $name): bool
    {
        return $this->adapter->has($name);
    }

    /**
     * Gets an attribute by key.
     *
     * @param string $name The attribute name
     * @param mixed|null $default The default value if not found
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        return $this->adapter->get($name, $default);
    }

    /**
     * Sets an attribute by key.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set(string $name, $value)
    {
        $this->adapter->set($name, $value);
    }

    /**
     * Sets multiple attributes at once: takes a keyed array and sets each key => value pair.
     *
     * @param array $values
     * @return void
     */
    public function replace(array $values)
    {
        $this->adapter->replace($values);
    }

    /**
     * Deletes an attribute by key.
     *
     * @param string $name
     * @return void
     */
    public function remove(string $name): void
    {
        $this->adapter->remove($name);
    }

    /**
     * Clear all attributes.
     */
    public function clear()
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
    public function save()
    {
        $this->adapter->save();
    }

    /**
     * Set session runtime options
     *
     * @param array $config
     * @return void
     * @link http://php.net/manual/en/session.configuration.php
     */
    public function setOptions(array $config)
    {
        $this->adapter->setOptions($config);
    }

    /**
     * Get session runtime options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->adapter->getOptions();
    }

    /**
     * Set cookie parameters.
     *
     * @link http://php.net/manual/en/function.session-set-cookie-params.php
     *
     * @param int $lifetime The lifetime of the cookie in seconds.
     * @param string $path The path where information is stored.
     * @param string $domain The domain of the cookie.
     * @param bool $secure The cookie should only be sent over secure connections.
     * @param bool $httpOnly The cookie can only be accessed through the HTTP protocol.
     * @return void
     */
    public function setCookieParams(int $lifetime, string $path, string $domain, bool $secure, bool $httpOnly): void
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
