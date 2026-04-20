<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#0B4D2C">
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
        <link rel="icon" type="image/png" href="{{ asset('images/kwatu_logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/kwatu_logo.png') }}">

        <title>oPOS | By Ori</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @filamentStyles
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-[#F4F7F5] px-4 py-8 sm:px-6 sm:py-12">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-6 flex justify-center">
                    <a href="/" aria-label="oPOS home">
                        <x-application-logo class="h-32 w-32 fill-current text-[#0B4D2C]" />
                    </a>
                </div>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    {{ $slot }}
                </section>

                <p class="mt-4 text-center text-xs text-slate-500">
                    Developed By:
                    <a href="https://www.oristudiozm.com" target="_blank" rel="noopener noreferrer" class="font-medium text-[#0B4D2C] hover:underline">Ori Studio Limited</a>
                </p>
            </div>
        </div>
        @filamentScripts
    </body>
</html>
