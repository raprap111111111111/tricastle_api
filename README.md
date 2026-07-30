<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Here is a clean, professional README.md file tailored for your project. You can copy and paste this directly into a file named README.md in the root of your tricastle_api folder.

Tricastle API
This is the API backend for the Tricastle project, built with Laravel. This project uses Laravel Sail for local development, providing a seamless Docker-based environment.

🚀 Getting Started
Follow these steps to get the project running on your local machine.

Prerequisites
Docker: Ensure Docker Desktop is installed and running on your machine.

PHP & Composer: Ensure Composer is installed.

Installation
Clone the repository (if you haven't already):

Bash
git clone <repository-url>
cd tricastle_api
Install PHP dependencies:

Bash
composer install
Install Laravel Sail (if not already configured):

Bash
composer require laravel/sail --dev
php artisan sail:install
(When prompted, select the services you wish to include, such as MySQL, Redis, etc.)

Start the development environment:

Bash
./vendor/bin/sail up -d

# Core packages
./vendor/bin/sail composer require \
    spatie/laravel-permission \
    spatie/laravel-activitylog \
    laravel/socialite \
    socialiteproviders/facebook \
    barryvdh/laravel-dompdf \
    laravel/horizon \
    laravel/telescope \
    league/flysystem-aws-s3-v3

# Dev packages
./vendor/bin/sail composer require --dev \
    laravel/pint \
    fakerphp/faker

# install 
    brew --version
    brew install mysql && \
    brew services start mysql && \
    sleep 10 && \
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS tricastle_db;" && \
    php artisan config:clear && \
    php artisan migrate
    composer require spatie/laravel-permission && \
    composer require spatie/laravel-activitylog && \
    composer require laravel/socialite && \
    composer require barryvdh/laravel-dompdf && \
    composer require laravel/horizon


    passport:client --password 

    user:tricastle
    password click enter

    raprap@Ralphs-MacBook-Air:~/Desktop/tricastle_bacolod/tricastle_api  $ sail artisan passport:client --password

  What should we name the client? [Tricastle]
❯ tricastle

  Which user provider should this client use to retrieve users?
  users .......................................................................................................................................... 0  
❯ 

         
 [ERROR] Value "" is invalid
         

  Which user provider should this client use to retrieve users?
  users .......................................................................................................................................... 0  
❯ developer

         
 [ERROR] Value "developer" is invalid
         

  Which user provider should this client use to retrieve users?
  users .......................................................................................................................................... 0  
❯ users

   INFO  New client created successfully.  

  Client ID ................................................................................................... 019f6f3e-706a-708d-99ca-50788a66a712  
  Client Secret ........................................................................................... DQJ2uyapQnrg6a8BAoIrFGqAK31sSzkZwhcydTMD  

   WARN  The client secret will not be shown again, so don't lose it!  

raprap@Ralphs-MacBook-Air:~/Desktop/tricastle_bacolod/tricastle_api  $ 


download 
# Image processing
composer require intervention/image

# AWS S3
composer require league/flysystem-aws-s3-v3
