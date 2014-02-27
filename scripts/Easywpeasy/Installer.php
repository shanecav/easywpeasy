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

  // Based on the default values already included in config/environments/local.php
  public static $DB_CONFIG = array(
    'Database Name' =>      'database_name',
    'Database User' =>      'database_user',
    'Database Password' =>  'database_password',
    'Database Host' =>      'database_host'
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
          $generate_salts = $io->askConfirmation('<info>Generate keys & salts in config/salts.php file? If not, you will have to edit this file yourself and add the key/salt constants. </info> [<comment>Y,n</comment>]? ', true);
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

        if ($io->isInteractive()) {
          $io->write('<info>Keys & salts successfully generated.</info>');
        }
    } else {
      return 1;
    }
  }

  public static function configureLocalEnvironment(Event $event) {
    if ($io->isInteractive()) {
      $root = dirname(dirname(__DIR__));
      $composer = $event->getComposer();
      $io = $event->getIO();
      $local_env_file_path = "{$root}/config/environments/local.php";
      $generate_local_env = $io->askConfirmation('<info>Would you like to enter the database connection information for your local environment now? </info> [<comment>Y,n</comment>]? ', true);
      if($generate_local_env) {
          foreach (self::$DB_CONFIG as $db_key => $db_value) {
            $local_db_value = $io->ask('<info>'.$db_key.'</info>', $db_value);
            $file_contents = file_get_contents($local_env_file_path);
            $file_contents = str_replace($db_value, $local_db_value, $file_contents);
            file_put_contents($local_env_file_path, $file_contents);
          }
          $io->write('<info>Local database connection info successfully added to config/environments/local.php.</info>');
      } else {
        return 1;
      }
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
