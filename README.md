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

After you've grabbed easywpeasy and put it in the folder of your choosing, `cd` into the folder you put it in, and run `composer install`. It'll ask if you want to automatically generate and add auth and salt keys. Unless you are a masochist who likes manually entering salt keys, just say yes.

### Removing version control files & folders (optional)

If you downloaded a .zip, then it shouldn't include any version control files or folders (e.g. .git, .gitignore, etc.). If you grabbed it via `git clone` or something similar, and you want to recursively delete any git files/folders in your current directory, you can use this handy little command ([source](http://montanaflynn.me/2013/03/devops/remove-all-git-files-recursively/)):

	find . | grep .git | xargs rm -rf

**Commands like this can be dangerous. I have used this one successfully, but use it at your own risk.**

Doing this is completely optional. I find it convenient because I often want to make my project into its own git repo after installing this.

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

You can adjust those terms by editing the `$environments` array in config/application.php. **If there are no matches, it assumes it is in a production environment.**

# Differences from a standard WordPress installation

If you're used to the folder structure of a standard WordPress installation, you'll notice a few differences here:

1. WordPress is installed inside the "wp" folder, instead of in the root.
2. However, the "wp-content" folder remains in the root. (There will also be a copy placed in the "wp" folder during the initial `composer install`, but that won't be used by anything, and can be deleted or ignored.)
3. As described above, the configuration is handled through the files in the "config" folder, and not directly through wp-config.php.
4. The "uploads" folder has been moved from inside the "wp-content" folder to the project root.

Otherwise, everything should still work the same as usual. Updates to plugins, themes, and to WordPress itself should still work just fine from within the WordPress Dashboard. Alternatively, you can use the `composer update` command to update WordPress and any plugins or other dependencies, provided you've updated the composer.json file with the proper information. However, you may find that updating in this way is a little less reliable, because of the inconsistent way versioning is handled in the WordPress plugin repository.

# Extra included stuff

## wp-cli

By default, [wp-cli](http://wp-cli.org/) is included as a dependency in the composer.json. It's not really required, but it is awesome. If you don't already use it, there are a few little steps to get it working on your local machine:

In order to use `wp` as a command in the shell from your project root, add the following to your .bash_profile:

	alias wp="./vendor/bin/wp"

If you're using MAMP, you'll also have to add the following line to your .bash_profile to make sure wp-cli is using MAMP's version of php (you can change the version number here as necessary, depending on what you have):

	export WP_CLI_PHP=/Applications/MAMP/bin/php/php5.4.10/bin/php

After you've set all that, you can run `wp db create` and it'll create a db based on the database info you set in your config files (of course, you'll need to have entered an existing mysql user/password with the proper database creation privileges). Now you're all set to go through the standard WordPress initial installation process. Remember that because WordPress is installed in the "wp" directory, you'll need to visit yourproject.com/**wp**/wp-admin/install.php.

When you're ready to copy your local database over to a different environment, wp-cli can help with that, too: Just use the `wp db export yourfilename.sql` command from your project root. This will place an SQL dump of your current environment's WordPress database in your project's root (of course you should change "yourfilename.sql" to whatever you want).

wp-cli has tons of other good stuff, too. Check out [their site](http://wp-cli.org/) for more info.

If you want, you can install wp-cli in your other environments, too. You'll have to refer to their site for instructions on how to set that up. I personally just use it locally, just to keep things simple, but there are a lot of potential benefits to using it in your other environments as well.

## Included plugins

I have the following plugins included in the composer.json file, because I use them on pretty much everything:

- advanced-custom-fields
- admin-menu-tree-page-view
- custom-post-type-ui
- easy-bootstrap-shortcodes
- post-types-order
- simple-page-ordering
- posts-to-posts
- backupwordpress

If you want to remove or add any, just edit the composer.json before you run `composer install`.

Because of the weird/inconsistent way the WordPress plugin repository does versioning, you'll probably have to update some of these from within the WordPress dashboard.