<!DOCTYPE html>
<html lang="es">
    <head>
        <title>@yield('titulo')</title>

        <!-- [Meta] -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
        <meta name="keywords" content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
        <meta name="author" content="CodedThemes">

        @include('layouts.style')

        {{-- manifest --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
        @include('layouts.navbar')
        @include('layouts.header')
        <div class="pc-container">
            <div class="pc-content">
                @yield('content')
            </div>
        </div>
        @include('layouts.footer_master')
        @include('layouts.script')
    </body>

</html>
