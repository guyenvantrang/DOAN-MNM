<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Trang quản trị')</title>
    <link rel="stylesheet" href="{{ asset('../resources/css/scroll.css') }}">

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    {{-- Header --}}
    @include('components.header')

    <div class="flex min-h-screen">
        {{-- Sidebar (nếu có) --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    {{-- Footer --}}
    @include('components.footer')

    {{-- ✅ Đưa yield script xuống đây --}}
    @yield('scripts_phantrang')
</body>

</html>