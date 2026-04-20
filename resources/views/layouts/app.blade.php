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
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @filamentStyles
    </head>
    <body class="antialiased" style="font-family: Inter, sans-serif;">
        <div x-data="{ sidebarOpen: false, userMenuOpen: false }" class="min-h-screen bg-[#F4F7F5] text-[#1A1A1A]">
            @php
                $currentUser = Auth::user();
                $displayName = $currentUser?->name ?? 'User';
                $firstName = explode(' ', trim($displayName))[0] ?: $displayName;
                $avatarUrl = $currentUser?->profile_photo_url;
                // Statutory Reminders removed
            @endphp

            <div class="flex min-h-screen">
                <aside class="hidden lg:flex lg:w-72 lg:flex-col lg:justify-between bg-[#0B4D2C] text-white">
                    <div>
                        <div class="px-6 py-6 border-b border-white/10">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                                <x-application-logo class="h-10 w-10 rounded-lg bg-white p-1" />
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-white/70">By Ori</p>
                                    <p class="font-bold text-lg leading-tight">oPOS</p>
                                </div>
                            </a>
                        </div>

                        <nav class="px-4 py-5 space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('accounting.dashboard') || request()->routeIs('pos.dashboard') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5.5v-6h-5v6H4a1 1 0 0 1-1-1v-10.5Z"/></svg>
                                <span>Dashboard</span>
                            </a>

                            @if (auth()->user()->isSalesperson())
                                <a href="{{ route('pos.sell') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('pos.sell') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                                    <span>New Sale</span>
                                </a>

                                <a href="{{ route('pos.sales.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('pos.sales.*') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 5h8m-8 5h5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/></svg>
                                    <span>Sales History</span>
                                </a>

                                <a href="{{ route('pos.petty-cash.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('pos.petty-cash.*') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2 7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Petty Cash</span>
                                </a>
                            @elseif (auth()->user()->isAdmin())
                                <a href="{{ route('admin.pos-products.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.pos-products.*') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <span>Inventory</span>
                                </a>

                                <a href="{{ route('admin.suppliers.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.suppliers.*') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2ZM8 7h8M8 11h6M8 15h4"/></svg>
                                    <span>Suppliers</span>
                                </a>

                                <a href="{{ route('reports.sales') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('reports.sales') || request()->routeIs('reports.suppliers-aging') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 14l3-3 3 2 4-5"/></svg>
                                    <span>Sales Report</span>
                                </a>

                                <a href="{{ route('admin.petty-cash.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.petty-cash.*') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2 7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Petty Cash</span>
                                </a>

                                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('admin.settings') || request()->routeIs('admin.users.*') || request()->routeIs('admin.categories.*') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm8 6a8 8 0 0 0-16 0"/></svg>
                                    <span>Settings</span>
                                </a>
                            @else
                                {{-- Accountant --}}
                                <a href="{{ route('reports.transactions') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('reports.transactions') || request()->routeIs('accounting.transactions.*') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 5h8m-8 5h5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/></svg>
                                    <span>Ledger</span>
                                </a>

                                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('reports.index') || request()->routeIs('reports.income-statement') || request()->routeIs('reports.balance-sheet') || request()->routeIs('reports.trial-balance') || request()->routeIs('reports.reconciliation') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 16V8m5 8V5m5 11v-6"/></svg>
                                    <span>Reports</span>
                                </a>

                                <a href="{{ route('accounting.settings') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ request()->routeIs('accounting.settings') || request()->routeIs('admin.accounts.*') || request()->routeIs('profile.*') ? 'bg-[#32CD32] text-[#0B4D2C] font-semibold' : 'text-white/85 hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm8 6a8 8 0 0 0-16 0"/></svg>
                                    <span>Settings</span>
                                </a>
                            @endif

                            <div class="mt-6 rounded-xl bg-white/10 p-3 text-white">
                                <p class="text-[10px] uppercase tracking-[0.14em] text-white/70">Time</p>
                                <p
                                    x-data="{ time: '', date: '', update() { const n = new Date(); this.time = n.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }); this.date = n.toLocaleDateString([], { day: '2-digit', month: 'short', year: 'numeric' }); } }"
                                    x-init="update(); setInterval(() => update(), 1000)"
                                    class="mt-1 text-lg font-bold leading-tight"
                                    x-text="time"
                                ></p>
                                <p class="text-xs text-white/75" x-text="date"></p>
                            </div>

                            <!-- Statutory Reminders removed -->
                        </nav>
                    </div>

                    <div class="px-4 py-4 border-t border-white/10 space-y-3">
                        <div class="flex items-center gap-3 rounded-xl bg-white/10 px-3 py-2">
                            <span class="h-9 w-9 shrink-0 overflow-hidden rounded-full border border-white/20 bg-white/10">
                                <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=0B4D2C&color=ffffff&size=128';">
                            </span>
                            <p class="truncate text-sm font-semibold text-white">Hi {{ $firstName }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block w-full rounded-xl border border-white/20 px-3 py-2 text-center text-sm text-white/90 hover:bg-white/10">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl border border-white/20 px-3 py-2 text-sm text-white/90 hover:bg-white/10">Sign Out</button>
                        </form>
                    </div>
                </aside>

                <div class="flex-1 flex flex-col min-w-0">
                    <header class="sticky top-0 z-20 border-b border-slate-200 bg-[#F4F7F5]/95 backdrop-blur">
                        <div class="px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-3">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden rounded-lg border border-slate-200 bg-white p-2 text-slate-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                            </button>

                            <p class="lg:hidden text-sm font-bold text-[#0B4D2C] whitespace-nowrap">oPOS</p>

                            @if (!auth()->user()->isSalesperson() && !auth()->user()->isAdmin())
                            <form method="GET" action="{{ route('reports.transactions') }}" class="hidden flex-1 md:block">
                                <label for="global_q" class="sr-only">Search</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3m1.3-5.2a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"/></svg>
                                    </span>
                                    <input id="global_q" name="q" type="search" value="{{ request('q') }}" placeholder="Search ledger entries or users..." class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-700 focus:border-[#32CD32] focus:ring-[#32CD32]">
                                </div>
                            </form>
                            @endif

                            <div class="relative ml-auto" @click.outside="userMenuOpen = false">
                                <button type="button" @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 rounded-lg px-1 py-1 hover:bg-slate-100">
                                    <span class="h-8 w-8 shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100 sm:h-9 sm:w-9">
                                        <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=0B4D2C&color=ffffff&size=128';">
                                    </span>
                                    <p class="max-w-[110px] truncate whitespace-nowrap text-xs font-semibold text-[#1A1A1A] sm:max-w-[170px] sm:text-sm">Hi {{ $firstName }}</p>
                                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                                </button>

                                <div x-show="userMenuOpen" x-transition style="display: none;" class="absolute right-0 z-30 mt-2 w-44 rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="space-y-6 p-4 pb-24 sm:p-6 sm:pb-24 lg:p-8 lg:pb-8">
                        @isset($header)
                            <section class="rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                                {{ $header }}
                            </section>
                        @endisset

                        {{ $slot }}
                    </main>
                </div>
            </div>

            <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur lg:hidden">
                <div class="grid grid-cols-5 text-xs">
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('accounting.dashboard') || request()->routeIs('pos.dashboard') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5.5v-6h-5v6H4a1 1 0 0 1-1-1v-10.5Z"/></svg>
                        <span>Dashboard</span>
                    </a>
                    @if (auth()->user()->isSalesperson())
                        <a href="{{ route('pos.sell') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('pos.sell') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                            <span>New Sale</span>
                        </a>
                        <a href="{{ route('pos.sales.index') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('pos.sales.*') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 5h8m-8 5h5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/></svg>
                            <span>Sales</span>
                        </a>
                        <a href="{{ route('pos.petty-cash.index') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('pos.petty-cash.*') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M2 7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span>Cash</span>
                        </a>
                    @elseif (auth()->user()->isAdmin())
                        <a href="{{ route('admin.pos-products.index') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('admin.pos-products.*') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Inventory</span>
                        </a>
                        <a href="{{ route('reports.sales') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('reports.sales') || request()->routeIs('reports.suppliers-aging') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 14l3-3 3 2 4-5"/></svg>
                            <span>Sales</span>
                        </a>
                        <a href="{{ route('admin.petty-cash.index') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('admin.petty-cash.*') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M2 7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span>Cash</span>
                        </a>
                    @else
                        <a href="{{ route('reports.transactions') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('reports.transactions') || request()->routeIs('accounting.transactions.*') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 5h8m-8 5h5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/></svg>
                            <span>Ledger</span>
                        </a>
                        <a href="{{ route('reports.index') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('reports.index') || request()->routeIs('reports.income-statement') || request()->routeIs('reports.balance-sheet') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16M7 16V8m5 8V5m5 11v-6"/></svg>
                            <span>Reports</span>
                        </a>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.settings') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('admin.settings') || request()->routeIs('admin.users.*') || request()->routeIs('admin.categories.*') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm8 6a8 8 0 0 0-16 0"/></svg>
                            <span>Settings</span>
                        </a>
                    @elseif (!auth()->user()->isSalesperson())
                        <a href="{{ route('accounting.settings') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('accounting.settings') || request()->routeIs('admin.accounts.*') || request()->routeIs('profile.*') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm8 6a8 8 0 0 0-16 0"/></svg>
                            <span>Settings</span>
                        </a>
                    @else
                        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center gap-1 px-2 py-2 {{ request()->routeIs('profile.*') ? 'text-[#0B4D2C] font-semibold' : 'text-slate-500' }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm8 6a8 8 0 0 0-16 0"/></svg>
                            <span>Profile</span>
                        </a>
                    @endif
                </div>
            </nav>

            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden" style="display: none;">
                <div class="absolute inset-0 bg-black/40" @click="sidebarOpen = false"></div>
                <aside class="relative z-50 h-full w-72 bg-[#0B4D2C] text-white p-4 flex flex-col">
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <x-application-logo class="h-8 w-8 rounded bg-white p-1" />
                            <span class="font-bold">oPOS</span>
                        </a>
                        <button @click="sidebarOpen = false" class="rounded p-1 hover:bg-white/10">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </div>

                    <nav class="mt-4 space-y-1 text-sm">
                        <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Dashboard</a>
                        @if (auth()->user()->isSalesperson())
                            <a href="{{ route('pos.sell') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">New Sale</a>
                            <a href="{{ route('pos.sales.index') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Sales History</a>
                            <a href="{{ route('pos.petty-cash.index') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Petty Cash</a>
                        @elseif (auth()->user()->isAdmin())
                            <a href="{{ route('admin.pos-products.index') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Inventory</a>
                            <a href="{{ route('admin.suppliers.index') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Suppliers</a>
                            <a href="{{ route('reports.sales') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Sales Report</a>
                            <a href="{{ route('admin.petty-cash.index') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Petty Cash</a>
                            <a href="{{ route('admin.settings') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Settings</a>
                        @else
                            <a href="{{ route('reports.transactions') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Ledger</a>
                            <a href="{{ route('reports.index') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Reports</a>
                            <a href="{{ route('accounting.settings') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Settings</a>
                        @endif
                    </nav>

                    <div class="mt-6 rounded-xl bg-white/10 p-3 text-white">
                        <p class="text-[10px] uppercase tracking-[0.14em] text-white/70">Time</p>
                        <p
                            x-data="{ time: '', date: '', update() { const n = new Date(); this.time = n.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }); this.date = n.toLocaleDateString([], { day: '2-digit', month: 'short', year: 'numeric' }); } }"
                            x-init="update(); setInterval(() => update(), 1000)"
                            class="mt-1 text-lg font-bold leading-tight"
                            x-text="time"
                        ></p>
                        <p class="text-xs text-white/75" x-text="date"></p>
                    </div>

                    <!-- Statutory Reminders removed -->

                    <div class="mt-auto pt-4 border-t border-white/10 space-y-3">
                        <div class="flex items-center gap-3 rounded-xl bg-white/10 px-3 py-2">
                            <span class="h-9 w-9 shrink-0 overflow-hidden rounded-full border border-white/20 bg-white/10">
                                <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=0B4D2C&color=ffffff&size=128';">
                            </span>
                            <p class="truncate text-sm font-semibold text-white">Hi {{ $firstName }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block w-full rounded-xl border border-white/20 px-3 py-2 text-center text-sm text-white/90 hover:bg-white/10">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl border border-white/20 px-3 py-2 text-sm text-white/90 hover:bg-white/10">Sign Out</button>
                        </form>
                    </div>
                </aside>
            </div>

            <div id="confirm-modal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
                <div id="confirm-backdrop" class="absolute inset-0 bg-slate-900/50"></div>
                <div class="relative z-10 flex min-h-full items-center justify-center p-4">
                    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl">
                        <div class="flex items-start gap-3">
                            <div id="confirm-icon" class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3Z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 id="confirm-title" class="text-base font-semibold text-slate-900">Confirm action</h3>
                                <p id="confirm-message" class="mt-1 text-sm text-slate-600">Are you sure you want to continue?</p>
                            </div>
                        </div>
                        <div class="mt-5 flex items-center justify-end gap-2">
                            <button id="confirm-cancel" type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                            <button id="confirm-submit" type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.getElementById('confirm-modal');
                if (!modal) return;

                const backdrop = document.getElementById('confirm-backdrop');
                const titleEl = document.getElementById('confirm-title');
                const messageEl = document.getElementById('confirm-message');
                const iconEl = document.getElementById('confirm-icon');
                const cancelBtn = document.getElementById('confirm-cancel');
                const confirmBtn = document.getElementById('confirm-submit');
                let pendingForm = null;

                function openModal() {
                    modal.classList.remove('hidden');
                    modal.setAttribute('aria-hidden', 'false');
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.setAttribute('aria-hidden', 'true');
                    pendingForm = null;
                }

                function applyVariant(variant) {
                    const isWarning = variant === 'warning';
                    iconEl.className = isWarning
                        ? 'mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600'
                        : 'mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600';
                    confirmBtn.className = isWarning
                        ? 'rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600'
                        : 'rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700';
                }

                document.addEventListener('submit', function (event) {
                    const form = event.target;
                    if (!(form instanceof HTMLFormElement)) return;

                    const message = form.dataset.confirmMessage;
                    if (!message) return;

                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }

                    event.preventDefault();
                    pendingForm = form;

                    titleEl.textContent = form.dataset.confirmTitle || 'Confirm action';
                    messageEl.textContent = message;
                    confirmBtn.textContent = form.dataset.confirmConfirmText || 'Confirm';
                    applyVariant(form.dataset.confirmVariant || 'danger');
                    openModal();
                }, true);

                confirmBtn.addEventListener('click', function () {
                    if (!pendingForm) return;
                    pendingForm.dataset.confirmed = '1';
                    const formToSubmit = pendingForm;
                    closeModal();
                    formToSubmit.requestSubmit();
                });

                cancelBtn.addEventListener('click', closeModal);
                backdrop.addEventListener('click', closeModal);

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') {
                        closeModal();
                    }
                });
            })();
        </script>
        @filamentScripts
        @livewire('notifications')
    </body>
</html>
