easywpeasy is an easy WordPress installer that uses [Composer](https://getcomposer.org/). A lot of it is based on roots/bedrock. Bedrock is awesome, but I needed something extremely quick, easy, and that can be easily deployed to a shared hosting environment without any extra remote configuration required.

I basically made this for my own needs, but I imagine it might be useful for others, as well.

# Prerequisites

You'll need [Composer](https://getcomposer.org/) to do the initial install - this will install WordPress and any dependencies (including WordPress plugins) that you set. I have a few plugins I use on every project already included in the composer.json file (see below), but you can edit that to your heart's desire.

### Installing Composer

If you're on OSX and using [Homebrew](http://brew.sh/), you can [install Composer via Homebrew](https://getcomposer.org/doc/00-intro.md#globally-on-osx-via-homebrew-):

1. Tap the homebrew-php repository into your brew installation if you haven't done so yet: `brew tap josegonzalez/homebrew-php`
2. Run `brew install josegonzalez/php/composer`.
3. Use Composer with the `composer` command.

If you're not on OSX or not using Homebrew, just follow the appropriate installation instructions on [Composer's 'Getting Started' page](https://getcomposer.org/doc/00-intro.md).

# Installation

Simply run `composer create-project shanecav/easywpeasy path/to/your/project`. This command will download easywpeasy to the location you specify, and then it will automatically run `composer install`. It will also prompt you for a couple initial configuration options:

1. It will ask if you'd like to automatically generate keys and salts for your config. Unless you have some reason you'd like to enter those manually, you should just choose yes. If you choose no, an empty file will be created at config/salts.php and you'll need to populate that file with the appropriate constant definitions (e.g. [https://api.wordpress.org/secret-key/1.1/salt/](https://api.wordpress.org/secret-key/1.1/salt/)).
2. It will then ask you if you'd like to set the database connection information for your local environment. If you choose yes, you will be prompted to enter the database name, user name, user password, and database host. Otherwise, you can set this information manually in config/environments/local.php.

Pro tip: If you chose yes on both of the above prompts, are using wp-cli (explained below), and entered a username & password for an existing local MySQL user with global priveleges in the above prompt, you can now `cd` into your project's root, run `wp db create` (to create a new database based on the connection information you entered), and you'll be all set to visit yourprojectslocaldomain.com/wp/wp-admin/install.php. That's a fresh WordPress install in three quick commands!

# Configuration

All of the WordPress configuration can be done via the files in the "config" folder (don't edit the wp-config.php in the project root, that's just used for imports).

### Environment-specific configuration

The files located in the "config/environments" folder control the environment specific settings (primarily the database connection info). By default, it includes local.php, stage.php, and production.php. You can add more if you'd like, just be sure to add them to the `$environments` array in config/application.php.

The environment config files are the only ones you actually have to set to get your WordPress install to work. If you'd like to edit other configs, you can do that too: 

### Base configuration (config/application.php)

The file config/application.php contains all of the base WordPress config settings that aren't specific to any environment.

It also includes the logic for determining which environment is currently active. It determines the environment by checking the current URL (`$_SERVER['SERVER_NAME']`), and searching it for environment-specific parts. The default logic is:

- If the URL contains ".local" or "local.", then it is a local environment.
- If the URL contains "stage.", then it is a staging environment.
- **If there are no matches, it assumes it is in a production environment.**

You can adjust those terms by editing the `$environments` array in config/application.php.

# Differences from a standard WordPress installation

If you're used to the folder structure of a standard WordPress installation, you'll notice a few differences here:

1. WordPress is installed inside the "wp" folder, instead of in the root.
2. However, the "wp-content" folder remains in the root. (There will also be a copy placed in the "wp" folder during the initial `composer install`, but that won't be used by anything, and can be deleted or ignored.)
3. As described above, the configuration is handled through the files in the "config" folder, and not directly through wp-config.php.

Otherwise, everything should still work the same as usual. Updates to plugins, themes, and to WordPress itself should still work just fine from within the WordPress Dashboard. Alternatively, you can use the `composer update` command to update WordPress and any plugins or other dependencies, provided you've updated the composer.json file with the proper information. However, you may find that updating in this way is a little less reliable, because of the inconsistent way versioning is handled in the WordPress plugin repository.

# Extra stuff

## wp-cli

I really recommend installing [wp-cli](http://wp-cli.org/). It only takes a second, and makes setting up new WordPress installs even faster.

If you have wpl-cli installed prior to using easywpeasy, you can cd into your newly created project's folder and run `wp db create` and it'll create a db based on the database info you set in your config files (of course, you'll need to have entered an existing mysql user/password with the proper database creation privileges). Now you're all set to go through the standard WordPress initial installation process. Remember that because WordPress is installed in the "wp" directory, you'll need to visit yourproject.com/**wp**/wp-admin/install.php.

When you're ready to copy your local database over to a different environment, wp-cli can help with that, too: Just use the `wp db export yourfilename.sql` command from your project root. This will place an SQL dump of your current environment's WordPress database in your project's root (of course you should change "yourfilename.sql" to whatever you want).

wp-cli has tons of other good stuff, too. Check out [their site](http://wp-cli.org/) for more info.

## Included plugins

I have the following plugins included in the composer.json file, because I use them on pretty much everything:

- Not included as a dependency because it's a commercial plugin, but I **strongly** recommend [Advanced Custom Fields Pro](http://www.advancedcustomfields.com/pro)
- wordpress-seo
- admin-menu-tree-page-view
- custom-post-type-ui
- easy-bootstrap-shortcodes
- post-types-order
- simple-page-ordering
- posts-to-posts
- backupwordpress
- be-subpages-widget
- better-menu-widget

If you want to remove or add any, just edit the composer.json before you run `composer install` (or `composer update`).

Because of the weird/inconsistent way the WordPress plugin repository does versioning, you'll probably have to update some of these from within the WordPress dashboard after your initial install.

## Included theme

I've included [my fork of the Roots theme](https://github.com/shanecav/roots) as a requirement in composer.json, because I use it as a starter for most of my WordPress projects. If you don't want to use it, you can ignore it, delete it, or remove it from composer.json before running `composer install`.