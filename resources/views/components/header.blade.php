<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Store Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    <!-- ========== Header ========== -->
    <header class="bg-white border-b border-gray-200 dark:bg-gray-900 dark:border-gray-700 shadow-sm">
        <nav class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            
            <!-- Logo -->
            <a href="#" class="flex items-center space-x-3">
                <img src="{{ asset('assets/header/logo.png') }}" class="h-8" alt="Watch Logo">
                <span class="text-2xl font-semibold dark:text-white">Watch Store</span>
            </a>

            <!-- Mobile Menu Button -->
            <button data-collapse-toggle="navbar-default" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Menu -->
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul
                    class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row 
                           md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">

                    <li><a href="#" class="block py-2 px-3 text-gray-900 hover:text-blue-600 dark:text-white">Dashboard</a></li>

                    <!-- Dropdown: Company -->
                    <li>
                        <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar"
                            class="flex items-center justify-between w-full py-2 px-3 text-gray-900 hover:text-blue-600 
                                   dark:text-white md:w-auto">
                            Company
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="dropdownNavbar"
                            class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">About us</a></li>
                                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Careers</a></li>
                                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Our Team</a></li>
                            </ul>
                        </div>
                    </li>

                    <li><a href="#" class="block py-2 px-3 text-gray-900 hover:text-blue-600 dark:text-white">Orders</a></li>
                    <li><a href="#" class="block py-2 px-3 text-gray-900 hover:text-blue-600 dark:text-white">Products</a></li>

                    <!-- User Dropdown -->
                    <li>
                        <button id="dropdownUserAvatarButton" data-dropdown-toggle="dropdownAvatar"
                            class="flex items-center py-2 px-3 text-gray-900 hover:text-blue-600 dark:text-white">
                            <img class="w-8 h-8 mr-2 rounded-full" src="{{ asset('assets/header/user.png') }}" alt="User Avatar">
                            Bonnie Green
                            <svg class="w-2.5 h-2.5 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <div id="dropdownAvatar"
                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
                            <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                <div class="font-medium">Pro User</div>
                                <div class="truncate">name@watchstore.com</div>
                            </div>
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a></li>
                                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Settings</a></li>
                                <li><a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Earnings</a></li>
                            </ul>
                            <div class="py-2">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200">Sign out</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</body>
</html>
