-- SIHADIR BACKEND --
1. cp .env.example .env
2. php artisan key:generate
3. Instalasi Paket JWT
    composer require php-open-source-saver/jwt-auth
4. Publik file konfigurasi 
    php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
5. Generate jwt key
    php artisan jwt:secret

