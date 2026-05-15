{{--
resources/views/filament/widgets/payment-overview.blade.php

Custom view for PaymentOverviewWidget to prevent Livewire re-rendering
Chart is wrapped with wire:ignore to isolate it from Livewire updates
--}}

<x-filament-widgets::chart-widget
    wire:ignore
    :heading="$getHeading()"
    :description="$getDescription()"
    :footer="$getFooter()"
    :maxHeight="$getMaxHeight()"
    :filters="$getFilters()"
    :columnSpan="$getColumnSpan()"
    :sort="$getSort()"
    :actions="$getActions()"
    :loadingIndicator="$isLoading()"
    :placeholder="$getPlaceholder()"
    :attributes="$attributes->merge($getExtraAttributes())"
>
    <canvas
        wire:ignore
        id="{{ $getId() }}"
        width="{{ $chartData ? '100%' : '0' }}"
        height="{{ $chartData ? '100%' : '0' }}"
        style="max-height: {{ $getMaxHeight() }}; {{ $chartData ? '' : 'display: none;' }}"
    ></canvas>

    @if($chartData)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('{{ $getId() }}');
                if (ctx && window.Chart) {
                    new Chart(ctx, {
                        type: '{{ $chartType }}',
                        data: @json($chartData),
                        options: @json($chartOptions)
                    });
                }
            });
        </script>
    @endif
</x-filament-widgets::chart-widget>