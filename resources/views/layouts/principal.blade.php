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

<body>
    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">

                <div class="auth-header">
                    <a href="#"><img src="../assets/images/logo/logo.png" style="with: 70px; height: 70px;" alt="img"></a>
                </div>

                @yield('content')

                @include('layouts.footer')
            </div>
        </div>
    </div>
    @include('layouts.script')
</body>

</html>
