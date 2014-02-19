<?php
$root_dir = dirname(dirname(__FILE__));
$server_name = $_SERVER['SERVER_NAME'];
$wp_home = 'http://' . $server_name;
$wp_siteurl = $wp_home . '/wp';

/**
* Define Environments - may be a string or array of options for an environment.
* This config will search $erver_name for the environment strings below
* to determine the current environment.
*/
$environments = array(
    'local'       => array('.local', 'local.'),
    'stage'       => 'stage.',
);

/**
* Determine current environment (don't edit this):
*/
foreach($environments AS $key => $env){
    if(is_array($env)){
        foreach ($env as $option){
            if(stristr($server_name, $option)){
                define('ENVIRONMENT', $key);
                break 2;
            }
        }
    } else {
        if(stristr($server_name, $env)){
            define('ENVIRONMENT', $key);
            break;
        }
    }
}
if(!defined('ENVIRONMENT')) define('ENVIRONMENT', 'production');
require_once dirname(__FILE__) . '/environments/' . ENVIRONMENT . '.php';

/**
 * Custom Content Directory
 */
define('CONTENT_DIR', '/wp-content');
define('WP_CONTENT_DIR', $root_dir . CONTENT_DIR);
define('WP_CONTENT_URL', WP_HOME . CONTENT_DIR);

/**
 * DB settings
 */
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
$table_prefix = 'wp_';

/**
 * WordPress Localized Language
 * Default: English
 *
 * A corresponding MO file for the chosen language must be installed to app/languages
 */
define('WPLANG', '');

/**
 * Authentication Unique Keys and Salts - If you chose to automatically generate these
 * during the initial 'composer install', then you don't need to do anything. If you did
 * not choose to automatically generate these, then this file will be empty. In that case, 
 * you'll need to manually copy+paste the keys from the following link:
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * into the "config/salts.php" file.
 */
require_once(dirname(__FILE__) . '/salts.php');

/**
 * Custom Settings
 */
// define('AUTOMATIC_UPDATER_DISABLED', true);
// define('DISABLE_WP_CRON', true);
// define('DISALLOW_FILE_EDIT', true);
define( 'UPLOADS', '/uploads' );

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
  define('ABSPATH', $root_dir . '/wp/');
}