# Email template editor for Filament 3.0

Содержание шаблона заполняется с помощью [Blade Templates](https://laravel.com/docs/12.x/blade)

## Установка

Установить пакет с помощью Composer.
```bash
composer require maksde/filament-email-templates
```

Выполнение команды установки. Опубликует файлы конфигураций, миграций в ваше приложение.
```bash
php artisan filament-email-templates:install
```

Опубликовать файлы конфигураций.
```bash
php artisan vendor:publish --tag="filament-email-templates-config"
```

Опубликовать файлы переводов.
```bash
 php artisan vendor:publish --tag="filament-email-templates-translations"
```

Опубликовать файлы миграций.
```bash
php artisan vendor:publish --tag="filament-email-templates-migrations"
```