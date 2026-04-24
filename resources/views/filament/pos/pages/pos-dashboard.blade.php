<x-filament-panels::page>
    <div class="flex justify-end mb-4">
        <button
            type="button"
            x-data
            x-on:click="$dispatch('openNewSale')"
            class="fi-btn fi-btn-size-lg inline-flex items-center gap-1.5 rounded-lg px-5 py-2.5 text-sm font-semibold shadow-sm bg-[#32CD32] text-[#0B4D2C] hover:bg-[#2db82d] transition"
        >
            <x-heroicon-o-shopping-cart class="h-5 w-5" />
            New Sale
        </button>
    </div>

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="$this->getWidgetData()"
        :widgets="$this->getVisibleWidgets()"
    />

    @livewire('pos-sale-modal')
</x-filament-panels::page>
