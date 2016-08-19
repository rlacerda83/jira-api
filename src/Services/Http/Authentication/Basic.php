<?php

namespace Jira\Services\Http\Authentication;

class Basic
{
    /**
     * Username
     * @var string
     */
    private $username;

    /**
     * Password
     * @var string
     */
    private $password;

    /**
     * @param string $username API Username
     * @param string $password API Password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Set username
     *
     * @access public
     * @param  string $username
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get current username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @access public
     * @param  string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get current password
     *
     * @access public
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
