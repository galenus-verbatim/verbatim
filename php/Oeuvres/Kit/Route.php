<?php
/**
 * Part of Teinte https://github.com/oeuvres/teinte
 * MIT License https://opensource.org/licenses/mit-license.php
 * Copyright (c) 2022 frederic.Glorieux@fictif.org
 * Copyright (c) 2013 Frederic.Glorieux@fictif.org & LABEX OBVIL
 * Copyright (c) 2012 Frederic.Glorieux@fictif.org
 * Copyright (c) 2010 Frederic.Glorieux@fictif.org
 *                    & École nationale des chartes
 */

declare(strict_types=1);

namespace Oeuvres\Kit;

Route::init();

class Route {
    /** root directory of the app = directory of index.php */
    static $php_dir;
    /** Path relative to the root app */
    static $url_request;
    /** Split of url parts */
    static $url_parts;
    /** Has a routage been done ? */
    static $routed;

    public static function init()
    {
        // get the caller file to resolve links
        self::$php_dir = dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR ;
        $url_request = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $url_request = strtok($url_request, '?'); // old
        # maybe not robust, get rel path from caller routes script
        $url_prefix = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        if (strpos($url_request, $url_prefix) !== FALSE) {
            $url_request = substr($url_request, strlen($url_prefix));
        }
        self::$url_request = $url_request;
        self::$url_parts = explode('/', ltrim($url_request, '/'));
    }
    public static function get($route, $php, $re=null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            self::route($route, $php, $re);
        }
    }
    public static function post($route, $php, $re=null)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            self::route($route, $php, $re);
        }
    }

    public static function route($route, $php, $re=null)
    {
        // the catchall
        if ($route == "/404") {
            self::$routed = true;
            include_once(self::$php_dir . "$php");
            exit();
        }
        // simple path
        if ($route == self::$url_request) {
            self::$routed = true;
            include_once(self::$php_dir . "$php");
            exit();
        }
        // special case, a route with a variable and no prefix, needs a regex
        $match = false;

        if (!$re);
        else if(!preg_match($re, self::$url_request)) {
            return;
        }
        else {
            $match = true;
        }



        // route may contain variables


        $route_parts = explode('/', ltrim($route, '/'));
        if (count($route_parts) != count(self::$url_parts)) {
            return;
        }
        for ($i = 0; $i < count($route_parts); $i++) {
            $rp = $route_parts[$i];
            if (preg_match("/^[$]/", $rp)) {
                $var = ltrim($rp, '$');
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // test if already exists ?
                    $_POST[$var] = self::$url_parts[$i];
                }
                else {
                    $_GET[$var] = self::$url_parts[$i];
                }
                $_REQUEST[$var] = self::$url_parts[$i];
            }
            else if ($route_parts[$i] != self::$url_parts[$i]) {
                return;
            }
        }
        self::$routed = true;
        include_once(self::$php_dir . "$php");
        exit();
    }

}
