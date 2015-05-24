<?php

/**
 * 쿠키 관련 클래스*
 * @brief
 * @see
 * @author noggong@gmail.com
 * @date 2015-04-18
 * @version 1.0
 */

class CookieHelper
{
    /**
     * 쿠키를 굽는다.
     * @brief 쿠키를 굽는다.
     */
    public static function bakeCookie($cookieName, $cookieContent, $expire = 0, $path = '/')
    {
        setcookie($cookieName, $cookieContent, $expire, '/');

    }


    /**
     * 쿠키를 초기화함.
     *
     * @brief 쿠키를 초기화함.
     */
    public static function clearCookie($cookieName)
    {
        @setcookie($cookieName, '', '0', '/');
    }


    /**
     * 쿠키를 초기화함.
     *
     * @brief 쿠키를 초기화함.
     */
    public static function getCookie($cookieName)
    {
        global $_COOKIE;
        $cookie_var = null;

        if (array_key_exists($cookieName, $_COOKIE)) {
            $cookie_var = $_COOKIE[$cookieName];
        }

        return $cookie_var;

    }

}
