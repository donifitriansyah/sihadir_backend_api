-- SIHADIR BACKEND --

1. Instalasi Paket JWT
    composer require php-open-source-saver/jwt-auth
2. Publik file konfigurasi 
    php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
3. Generate jwt key
    php artisan jwt:secret

