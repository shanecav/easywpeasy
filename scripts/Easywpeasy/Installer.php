<?php

namespace Easywpeasy;

use Composer\Script\Event;

class Installer {
  public static $KEYS = array(
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT'
  );

  public static function addSalts(Event $event) {
    $root = dirname(dirname(__DIR__));
    $composer = $event->getComposer();
    $io = $event->getIO();
    $salts_file = @fopen("{$root}/config/salts.php", "x");
    if($salts_file) {
        if (!$io->isInteractive()) {
          $generate_salts = $composer->getConfig()->get('generate-salts');
        } else {
          $generate_salts = $io->askConfirmation('<info>Generate salts in config/salts.php file? If not, you will have to edit this file yourself and add the auth key/salt constants. </info> [<comment>Y,n</comment>]? ', true);
        }

        if (!$generate_salts) {
          return 1;
        }

        $salts = array_map(function ($key) {
          return sprintf("define('%s', '%s');", $key, Installer::generate_salt());
        }, self::$KEYS);

        fwrite($salts_file, "<?php \n");
        fwrite($salts_file, implode($salts, "\n")); 
        
        fclose($salts_file); 
    } else {
      return 1;
    }
  }

  /**
   * Slightly modified/simpler version of wp_generate_password
   * https://github.com/WordPress/WordPress/blob/cd8cedc40d768e9e1d5a5f5a08f1bd677c804cb9/wp-includes/pluggable.php#L1575
   */
  public static function generate_salt($length = 64) {
    $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $chars .= '!@#$%^&*()';
    $chars .= '-_ []{}<>~`+=,.;:/?|';

    $salt = '';
    for ($i = 0; $i < $length; $i++) {
      $salt .= substr($chars, rand(0, strlen($chars) - 1), 1);
    }

    return $salt;
  }
}
