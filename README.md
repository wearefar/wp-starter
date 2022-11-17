# A modern stack for WordPress development

## Minimum Requirements
This theme requires PHP 8 and MySQL 5.7+.

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

To install all nodeJS dependencies run

```sh
npm install
```

and then

```sh
# Start the dev server...
npm run dev

# Build for production...
npm run build
```
