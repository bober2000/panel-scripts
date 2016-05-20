<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'vendor/autoload.php';

/**
 * Generate a random string
 *
 * @link https://paragonie.com/b/JvICXzh_jhLyt4y3
 *
 * @param int $length - How long should our random string be?
 * @param string $charset - A string of all possible characters to choose from
 * @return string
 */
function random_str($length = 32, $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    // Type checks:
    if (!is_numeric($length)) {
        throw new InvalidArgumentException(
            'random_str - Argument 1 - expected an integer'
        );
    }
    if (!is_string($charset)) {
        throw new InvalidArgumentException(
            'random_str - Argument 2 - expected a string'
        );
    }

    if ($length < 1) {
        // Just return an empty string. Any value < 1 is meaningless.
        return '';
    }
    // This is the maximum index for all of the characters in the string $charset
    $charset_max = strlen($charset) - 1;
    if ($charset_max < 1) {
        // Avoid letting users do: random_str($int, 'a'); -> 'aaaaa...'
        throw new LogicException(
            'random_str - Argument 2 - expected a string at least 2 characters long'
        );
    }
    // Now that we have good data, this is the meat of our function:
    $random_str = '';
    for ($i = 0; $i < $length; ++$i) {
        $r = random_int(0, $charset_max);
        $random_str .= $charset[$r];
    }
    return $random_str;
}
/**
 * Return the user's home directory.
 */

function drush_server_home() {
  // Cannot use $_SERVER superglobal since that's empty during UnitUnishTestCase
  // getenv('HOME') isn't set on Windows and generates a Notice.
  $home = getenv('HOME');
  if (!empty($home)) {
    // home should never end with a trailing slash.
    $home = rtrim($home, '/');
  }
  elseif (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
    // home on windows
    $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
    // If HOMEPATH is a root directory the path can end with a slash. Make sure
    // that doesn't happen.
    $home = rtrim($home, '\\/');
  }
  return empty($home) ? NULL : $home;
}

try {
    $user_home = drush_server_home();
    $mysql_settings = parse_ini_file($user_home.'/.my.cnf', true);
} catch (Exception $ex) {
    die('Missing file: ' . $iniFile);
}

$pure_pwdfile = getenv('PURE_PASSWDFILE');
if (empty($pure_pwdfile))
  $pure_pwdfile='/etc/pure-ftpd/pureftpd.passwd';
