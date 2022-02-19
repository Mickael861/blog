<?php
namespace App\Core;

class Access
{
    const USER_KEY = 'user_id';
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'utilisateur';
    
    /**
     * Manage the accessibility of the admin part
     *
     * @return bool true if he has the admin role, false otherwise
     */
    public static function userIsAdmin(): bool
    {
        $is_admin = false;
        $datasSession = self::getSession();

        if (!empty($datasSession[self::USER_KEY]) && $datasSession['role'] === self::ROLE_ADMIN) {
            $is_admin = true;
        }

        return $is_admin;
    }
    
    /**
     * Start the session
     *
     * @return void
     */
    public static function startSession(): void
    {
        session_start();
    }
    
    /**
     * Retrieve session datas
     *
     * @return array
     */
    public static function getSession(): array
    {
        $session = $_SESSION;
        $datasSession = array();

        foreach ($session as $key => $value) {
            $datasSession[htmlentities($key)] = htmlentities($value);
        }

        return $datasSession;
    }
    
    /**
     * Know if the session is open
     *
     * @return bool true if the session is open, false otherwise
     */
    public static function sessionIsStart(): bool
    {
        $session = self::getSession();
        $is_start = false;

        if (!empty($session)) {
            $is_start = true;
        }

        return $is_start;
    }
    
    /**
     * Add session data
     *
     * @param  array $datas Data to be recorded
     * @return void
     */
    public static function setDatasSession(array $datas): void
    {
        if (!empty($datas)) {
            foreach ($datas as $key => $value) {
                $_SESSION[$key] = htmlentities($value);
            }
        }
    }
    
    /**
     * Destroy the session
     *
     * @return void
     */
    public static function sessionDestroy(): void
    {
        session_destroy();
    }
}
