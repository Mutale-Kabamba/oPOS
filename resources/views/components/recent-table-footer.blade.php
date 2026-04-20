@props([
    'total' => 0,
    'preview' => 5,
    'state' => 'showAll',
])

<div class="flex items-center justify-between border-t border-slate-200 px-4 py-3">
    <p class="text-xs text-slate-500">
        Showing
        <span x-text="{{ $state }} ? '{{ (int) $total }}' : '{{ min((int) $preview, (int) $total) }}'"></span>
        of {{ (int) $total }}
    </p>

    @if ((int) $total > (int) $preview)
        <button
            type="button"
            @click="{{ $state }} = !{{ $state }}"
            class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50"
            x-text="{{ $state }} ? 'Show Less' : 'View More'"
        ></button>
    @endif
</div>
