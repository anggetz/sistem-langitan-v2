<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- CSS -->
        <link href="assets/vendors/apexcharts/apexcharts.css" rel="stylesheet"/>
        <link href="assets/vendors/keenicons/styles.bundle.css" rel="stylesheet"/>
        <link href="assets/css/styles.css" rel="stylesheet"/>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="antialiased flex h-full text-base text-gray-700 [--tw-page-bg:#fefefe] [--tw-page-bg-dark:var(--tw-coal-500)] demo1 sidebar-fixed header-fixed bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]">
        
        <!-- Theme Mode -->
        <script>
        const defaultThemeMode = 'light'; // light|dark|system
            let themeMode;

            if ( document.documentElement ) {
                if ( localStorage.getItem('theme')) {
                        themeMode = localStorage.getItem('theme');
                } else if ( document.documentElement.hasAttribute('data-theme-mode')) {
                    themeMode = document.documentElement.getAttribute('data-theme-mode');
                } else {
                    themeMode = defaultThemeMode;
                }

                if (themeMode === 'system') {
                    themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }

                document.documentElement.classList.add(themeMode);
            }
        </script>
        <!-- End of Theme Mode -->
         
        @inertia

        <!-- Scripts -->
        <script src="assets/js/core.bundle.js">
        </script>
        <script src="assets/vendors/apexcharts/apexcharts.min.js">
        </script>
        <script src="assets/js/widgets/general.js">
        </script>
        <script src="assets/js/layouts/demo1.js">
        </script>
        <!-- End of Scripts -->
         
    </body>
</html>
