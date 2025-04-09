# Laravel 12 + Inertia Vue + Metronic Theme

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Jangan menggunakan branch `template`

Branch ini hanya untuk pembuatan template saja dan digunakan untuk mengambil template component jadi yang akan dipasang di aplikasi.

## Requirements

- PHP 8.2
- [Composer](https://getcomposer.org)
- [Node LTS](https://nodejs.org)

## Development Setup

1. Install Node modules
   ```shell
   npm i
   ```

2. Install Composer vendors
   ```shell
   composer install
   ```

3. Run development tools
   ```shell
   composer run dev
   ```

## Documentation

### Breadcrumbs

Breadcrumbs dibuild secara otomatis, apabila ingin merubah breadcrumb, assign variabel `breadcrumbs` pada saat merender Vue page

```php
class MyController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Page', [
            'breadcrumbs' => [
                ['Dashboard', '/dashboard'],
                ['Page', '/dashboard'],
                ['New Data / Update']
            ]
        ]);
    }
}
```

### Role, Menu & Sub-Menu

Menu pada Sidebar di-render dari data session `auth.role`. Variabel `role` di-assign ke session setelah login dan di-destroy
setelah logout.

```
Hirarki:
$user->role->menu[]->submenu[]
```
