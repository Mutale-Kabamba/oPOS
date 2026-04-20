<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Monitoring</p>
            <h2 class="text-xl font-bold text-[#0B4D2C] leading-tight">Live Watch</h2>
        </div>
    </x-slot>

    <div class="space-y-4 max-w-5xl">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-600">Track live operational and finance events from one place. This module is ready for your custom widgets (cash movement feed, invoice alerts, and collection updates).</p>
        </section>

        <section class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Cash Activity</p>
                <p class="mt-2 text-2xl font-bold text-[#1A1A1A]">Live</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Collections</p>
                <p class="mt-2 text-2xl font-bold text-[#1A1A1A]">Live</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Alerts</p>
                <p class="mt-2 text-2xl font-bold text-[#1A1A1A]">Live</p>
            </article>
        </section>
    </div>
</x-app-layout>
