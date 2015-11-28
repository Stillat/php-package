# PHP Package

PHP Package is a template for the [NewUp](https://github.com/newup/newup) package generator. The NewUp generator is a general-purpose tool for creating things from templates; PHP Package is a template that provides you with a starting point for your PHP projects.

* [Installation](#installation)
* [General Usage](#usage)
* [Specifying a PHP Version](#phpv)
* [PHPUnit Integration](#phpunit)
* [TravisCI Integration](#travis)
* [License](#license)

<a name="installation"></a>
## Installation

First, make sure you have [NewUp](https://github.com/newup/newup) installed and configured. Afterwards, you can run this command:

`newup template:install stillat/php-package`

NewUp will then install and configure everything it needs to internally for the `php-package` template.

<a name="usage"></a>
## General Usage

After you have installed the `php-package` template, we are ready to create a new PHP Package:

`newup a stillat/php-package vendor/package <output_directory>`

In the above example, replace `vendor/package` with the vendor and package name of your new package (for example `stillat/php-package`) and replace `<output_directory>` with the directory you want the package to be created in.

Used with no options, PHP Package will create a directory/file structure similar to the following:

~~~
src/
.gitignore
composer.json
~~~

The `src/` directory is where you will create your new package/library. It is empty so you have a fresh starting point. This directory will be autoloaded by Composer using the `psr-0` autoloader.

The `.gitignore` file contains quite a few options set for you by default, with instructions on how to remove configured options and where to find more.

The `composer.json` file is your standard `composer.json` file. If you configured NewUp with your name and email address, the authors field will already be filled in for you.

<a name="phpv"></a>
## Specifying a PHP version

You can specify a PHP version that your package requires by providing an extra parameter when generating your package:

`newup a stillat/php-package vendor/package <output_directory> <php_version>`

By default, the PHP version is set to `>=5.5.9`.

For example, we could easily state that our package needs at least PHP 5.6 when creating our package (pay special attention to the quotes!):

`newup a stillat/php-package vendor/package <output_directory> ">=5.6"`

<a name="phpunit"></a>
## PHPUnit Integration

To enable PHPUnit support on your generated package, just add the "--phpunit" switch to the end of the command:

`newup a stillat/php-package vendor/package <output_directory> --phpunit`

The following additional directories/files will be created for you:

~~~
tests/
tests/ExampleTest.php
phpunit.xml
~~~

The `tests/` directory will be where your tests will live. An example test (`tests/ExampleTest.php` is already included in this directory (this can be safely deleted).

The `phpunit.xml` file contains the configuration for PHPUnit. It is already configured with sensible defaults and features a customized test suite name.

<a name="travis"></a>
## TravisCI Integration

If you would like to rapidly configure TravisCI for your project, just add the `--travis` switch to the end of the command:

`newup a stillat/php-package vendor/package <output_directory> --travis`

You will see interactive prompts that will guide you through the configuration process. An example session might look something like this:

~~~
Would you like to add a PHP version to test? [Y/n] Y
Which PHP version would you like to test? 5.5.9
Do you want to allow failures for PHP version 5.5.9? [y/N] N

Would you like to add a PHP version to test? [Y/n] Y
Which PHP version would you like to test? 5.6
Do you want to allow failures for PHP version 5.6? [y/N] N

Would you like to add a PHP version to test? [Y/n] Y
Which PHP version would you like to test? 7.0
Do you want to allow failures for PHP version 7.0? [y/N] Y

Would you like to add a PHP version to test? [Y/n] Y
Which PHP version would you like to test? hhvm
Do you want to allow failures for PHP version hhvm? [y/N] N

Would you like to add a PHP version to test? [Y/n] N
~~~

This would generate a `.travis.yml` file similar to the following:

~~~
language: php

php:
  - 5.5.9
  - 5.6
  - 7.0
  - hhvm

matrix:
    allow_failures:
        - php: 7.0

sudo: false

install: travis_retry composer install --no-interaction --prefer-source
~~~

### TravisCI and PHPUnit

If you specify both the `--travis` and the `--phpunit` flags, the following script will be added to the end of your `.travis.yml` file automatically for you:

`script: vendor/bin/phpunit`

<a name="license"></a>
## License

Licensed under the MIT License. Enjoy!