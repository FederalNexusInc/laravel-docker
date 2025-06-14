<x-filament::page>
    @if (session()->has('error'))
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <select
                wire:model.live="selectedAnchorId"
                class="form-select border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
            >
                @foreach ($anchors as $anchor)
                    <option value="{{ $anchor->anchor_id }}">
                        {{ $anchor->lead_shaft_od }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Session Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if (isset($data['ErrorMessage']) && $data['ErrorMessage'])
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <select
                wire:model.live="selectedAnchorId"
                class="form-select border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
            >
                @foreach ($anchors as $anchor)
                    <option value="{{ $anchor->anchor_id }}">
                        {{ $anchor->lead_shaft_od }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Data Error!</strong>
            <span class="block sm:inline">{{ $data['ErrorMessage'] }}</span>
        </div>
    @endif

    @if (count($data) > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <select
                wire:model.live="selectedAnchorId"
                class="form-select border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
            >
                @foreach ($anchors as $anchor)
                    <option value="{{ $anchor->anchor_id }}">
                        {{ $anchor->lead_shaft_od }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex-1">
                <h2 class="text-xl font-bold mb-2 text-center">Helical Pile/Anchor Information</h2>
                <p class="text-center">Req. Allowable Pile Capacity: {{ round($data['RequiredAllowableCapacity'] ?? 0, 2) }} kip</p>
                <p class="text-center">Applied Factor of Safety: {{ round($data['RequiredSafetyFactor'] ?? 0, 2) }}</p>
                <p class="text-center">Helical Pile Diameter: {{ $data['HelicalPileDiameter'] ?? 0 }} in</p>
                <p class="text-center">Helix Configuration: {{ $data['HelixConfiguration'] ?? 'N/A' }}</p>
                <p class="text-center">Torque Correlation Factor: {{ round($data['EmpericalTorqueFactor'] ?? 0, 2) }} lbs/ft-lbs</p>
            </div>

            <div class="flex-1">
                <h2 class="text-xl font-bold mb-2 text-center">Estimated Pile Capacity</h2>
                <p class="text-center">Allowable Frictional Resistance: {{ round($data['AllowableFrictionalResistance'] ?? 0, 2) }} kip</p>
                <p class="text-center">Allowable End Bearing Capacity: {{ round($data['AllowableEndBearing'] ?? 0, 2) }} kip</p>
                <p class="text-center">Allowable Pile Capacity: {{ round($data['AllowablePileCapacity'] ?? 0, 2) }} kip</p>
                <p class="text-center">Approximate Pile Embedment Depth: {{ round($data['ApproximatePileEmbedmentDepth'] ?? 0, 2) }} ft</p>
                <p class="text-center">Required Min. Installation Torque: {{ round($data['RequiredInstallationTorque'] ?? 0, 2) }} ft-lbs</p>
            </div>
        </div>
        <div>
        @if (count($this->getFilteredResults()) > 0)
            <table class="w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b">Depth (ft)</th>
                        <th class="px-4 py-2 border-b">Ultimate Anchor Capacity (lbs)</th>
                        <th class="px-4 py-2 border-b">Torsional Resistance (lb-ft)</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($this->minDepth > 1)
                        <tr
                            class="cursor-pointer hover:bg-gray-50"
                            wire:click="expandAbove"
                        >
                            <td colspan="3" class="px-4 py-2 border-b text-center text-primary-600 font-medium">
                                Expand Above
                            </td>
                        </tr>
                    @endif

                    @foreach ($this->getFilteredResults() as $depth => $results)
                        <tr>
                            <td class="px-4 py-2 border-b text-center">{{ $depth }}</td>
                            <td class="px-4 py-2 border-b text-center">{{ round($results['anchor_capacity'], 2) }} lbs</td>
                            <td class="px-4 py-2 border-b text-center">{{ round($results['torsional_resistance'], 2) }} lb-ft</td>
                        </tr>
                    @endforeach

                    @if ($this->maxDepth < max(array_keys($data['DepthResults'])))
                        <tr
                            class="cursor-pointer hover:bg-gray-50"
                            wire:click="expandBelow"
                        >
                            <td colspan="3" class="px-4 py-2 border-b text-center text-primary-600 font-medium">
                                Expand Below
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @else
            <p>No depth-specific results available.</p>
        @endif
        </div>
        <div class="">
            @livewire(App\Filament\Resources\ProjectResource\Widgets\AnchorResult::class, [
                'title' => $data['CalculationType']->value,
                'depth' => array_keys($this->data['DepthResults']),
                'anchorCapacity' => array_map(fn($r) => round($r['anchor_capacity'], 2), $this->data['DepthResults']),
                'torsionalResistance' => array_map(fn($r) => round($r['torsional_resistance'], 2), $this->data['DepthResults']),
            ])
        </div>
    @else
        <p>No data available.</p>
    @endif
    <!-- Trigger for saving the chart image -->

    @push('scripts')
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    const canvas = document.querySelector('canvas');
                    // console.log("Save Chart");
                    if (canvas) {
                        const base64Image = canvas.toDataURL('image/png');
                        // console.log(base64Image);
                        window.Livewire.dispatch('saveChartImage', { base64Image });
                    } else {
                        alert("Canvas not found!");
                    }
                }, 1000);
            });
        </script>
    @endpush
</x-filament::page>