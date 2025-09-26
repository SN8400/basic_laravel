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

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## Setting up project
copy .env.example .env

APP_NAME="AE Stock"
APP_ENV=production
APP_URL=http://<host>:<port>   # หรือโดเมนจริง

## Srcipt Create Table
-- ลบตารางเก่าถ้ามีอยู่แล้ว
IF OBJECT_ID('[dbo].[users]', 'U') IS NOT NULL
    DROP TABLE [dbo].[users];
GO

-- สร้างตาราง users
CREATE TABLE [dbo].[users] (
    [id]             INT IDENTITY(1,1) PRIMARY KEY,
    [name]           NVARCHAR(255)    NOT NULL,
    [username]       NVARCHAR(100)    NOT NULL UNIQUE,
    [email]          NVARCHAR(255)    NOT NULL UNIQUE,
    [password]       NVARCHAR(255)    NOT NULL,
    [remember_token] NVARCHAR(100)    NULL,
    [is_active]      BIT              NOT NULL DEFAULT(1),
    [created_at]     DATETIME2        NOT NULL DEFAULT SYSDATETIME(),
    [updated_at]     DATETIME2        NOT NULL DEFAULT SYSDATETIME(),
);
GO

IF OBJECT_ID('[dbo].[personal_access_tokens]', 'U') IS NULL
BEGIN
  CREATE TABLE [dbo].[personal_access_tokens](
      [id]              BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
      [tokenable_type]  NVARCHAR(255) NOT NULL,
      [tokenable_id]    BIGINT        NOT NULL,
      [name]            NVARCHAR(255) NOT NULL,
      [token]           NVARCHAR(64)  NOT NULL UNIQUE,   -- Sanctum เก็บ hash 64 ตัว
      [abilities]       NVARCHAR(MAX) NULL,
      [last_used_at]    DATETIME2     NULL,
      [expires_at]      DATETIME2     NULL,
      [created_at]      DATETIME2     NOT NULL DEFAULT SYSDATETIME(),
      [updated_at]      DATETIME2     NOT NULL DEFAULT SYSDATETIME()
  );
  CREATE INDEX IX_pat_tokenable
    ON [dbo].[personal_access_tokens]([tokenable_type],[tokenable_id]);
END

DB_CONNECTION=sqlsrv|mysql
DB_HOST=...
DB_PORT=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...


composer install --no-interaction --prefer-dist
php artisan key:generate
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache


##Create First User
cd Project Path
php artisan tinker --execute
use Illuminate\Support\Facades\Hash;
use App\Models\User;

 User::updateOrInsert(
   [
     'name'       => 'Laco Admin',
     'email'      => 'admin@local',
     'username'      => 'admin',
     'password'   => Hash::make('P@ssw0rd1993'),
     'is_active'      => 1,
     'created_at' => now(),
     'updated_at' => now()
   ]
 );

php artisan serve --host=127.0.0.1 --port=8080