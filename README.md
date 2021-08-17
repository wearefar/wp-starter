# A modern stack for WordPress development

## Minimum Requirements
This theme required PHP 7.3+ and MySQL 5.7+.

## Installation

We utilize [Composer](https://getcomposer.org/) to manage dependencies. Make sure you have Composer installed on your machine.

Run `composer install`. This will install all dependencies including WordPress and plugins.

## Configuration

Copy `.env.example` to `.env` and set your environment variables, including database credentials in the `.env` file:

```
DB_NAME=database
DB_USER=username
DB_PASSWORD=password
```

Add also the Salt Keys to your environment file. [Visit the salt key generator](https://wordplate.github.io/salt) and copy the randomly generated keys to your `.env` file.

### Public Directory

After installing, you should configure your web server's document / web root to be the `public` directory. The `index.php` in this directory serves as the front controller for all HTTP requests entering your application.

## Building Assets

[Laravel Mix](https://github.com/JeffreyWay/laravel-mix/tree/master/docs#readme) is a clean layer on top of Webpack to make the 80% use case laughably simple to execute.

To install all nodeJS dependencies run

```sh
npm install
```

Then you can

```sh
// Run all mix tasks...
npm run watch

// Run all mix tasks and minify output...
npm run prod
```
