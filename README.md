# Sistem Langitan v2

Sistem Langitan v2 merupakan sistem informasi akademik perguruan tinggi yang dikembangkan oleh Universitas Maarif Hasyim Latif. 

## Requirements

- PHP 8.2
- [Composer](https://getcomposer.org)
- [Node LTS](https://nodejs.org)

## Frameworks

- [Laravel 12](https://laravel.com/docs)
- [InertiaJS](https://inertiajs.com)
- [Metronic 9 Theme](https://keenthemes.com/metronic/tailwind/docs/)

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

### Relasi Route, Controller, & Menu/Sub-Menu

Format url tiap halaman yg ada di menu: `/[role]/[menu]/[sub-menu]`.
URL tersebut perlu ditranslasikan melalui route dan controller.

- `[role]`: berelasi dengan nama role, yang mempunyai 1 file router `[role].php` di folder `routes/`,
  dan wajib di-require di `web.php` 
- `[menu]`: berelasi dengan controller `[nama-menu]Controller` yang berada di folder `app/Http/Controllers/[role]/`
- `[sub-menu]`: berelasi dengan method/function yang ada di dalam `[nama-menu]Controller`
- (Opsional). Apabila akan diakses di sebuah halaman Vue, pastikan route diberi nama dengan format: `role.menu.sub-menu`

Sebagai contoh di role mahasiswa, url: `/mahasiswa/biodata/data`, maka route, controller, dan function yang harus di buat

```php
/// routes/mahasiswa.php

use App\Http\Controllers\Mahasiswa\BiodataController;

Route::group(['prefix' => 'mahasiswa', 'middleware' => 'role:' . Role::MAHASISWA], function () {

    // Route yg perlu ditambahkan
    Route::get('/biodata/data', [BiodataController::class, 'data'])->name('mahasiswa.biodata.data');

});
```

```php
/// app/Http/Controllers/Mahasiswa/BiodataController.php

class BiodataController extends Controller
{
    public function data()
    {
        // Code here
    }
}
```

### Resource Controller untuk CRUD (Rekomendasi)

Untuk halaman-halaman yang bersifat master CRUD, pembuatan route, controller, & function bisa langsung memanfaatkan
resource controller bawaan Laravel (Referensi: https://laravel.com/docs/12.x/controllers#resource-controllers).

Sebagai contoh, master Semester di role Pendidikan. Maka route nya akan menjadi seperti berikut:
```php
/// routes/pendidikan.php

use App\Http\Controllers\Pendidikan\SemesterController;

Route::group(['prefix' => 'pendidikan', 'middleware' => 'role:' . Role::PENDIDIKAN], function () {

    // Route yg perlu ditambahkan
    Route::resource('/semester', SemesterController::class);

});
```

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
