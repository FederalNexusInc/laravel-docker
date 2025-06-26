<x-filament::page>
    @if (session()->has('error'))
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <select
                wire:model.live="selectedAnchorId"
                class="form-select border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-800"
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
                class="form-select border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-800"
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
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 ">
            <select
                wire:model.live="selectedAnchorId"
                class="form-select border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm dark:bg-gray-900 dark:border-gray-800"
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
                <h2 class="text-xl font-bold mb-2 text-left">Helical Pile/Anchor Information</h2>
                <p class="text-left"><strong>Req. Allowable Pile Capacity:</strong> {{ round($data['RequiredAllowableCapacity'] ?? 0, 2) }} kip</p>
                <p class="text-left"><strong>Applied Factor of Safety:</strong> {{ round($data['RequiredSafetyFactor'] ?? 0, 2) }}</p>
                <p class="text-left"><strong>Helical Pile Diameter:</strong> {{ $data['HelicalPileDiameter'] ?? 0 }} in</p>
                <p class="text-left"><strong>Helix Configuration:</strong> {{ $data['HelixConfiguration'] ?? 'N/A' }}</p>
                <p class="text-left"><strong>Torque Correlation Factor:</strong> {{ round($data['EmpericalTorqueFactor'] ?? 0, 2) }} lbs/ft-lbs</p>
            </div>

            <div class="flex-1">
                <h2 class="text-xl font-bold mb-2 text-left">Estimated Pile Capacity</h2>
                <p class="text-left"><strong>Allowable Frictional Resistance:</strong> {{ round($data['AllowableFrictionalResistance'] ?? 0, 2) }} kip</p>
                <p class="text-left"><strong>Allowable End Bearing Capacity:</strong> {{ round($data['AllowableEndBearing'] ?? 0, 2) }} kip</p>
                <p class="text-left"><strong>Allowable Pile Capacity:</strong> {{ round($data['AllowablePileCapacity'] ?? 0, 2) }} kip</p>
                <p class="text-left"><strong>Approximate Pile Embedment Depth:</strong> {{ round($data['ApproximatePileEmbedmentDepth'] ?? 0, 2) }} ft</p>
                <p class="text-left"><strong>Required Min. Installation Torque:</strong> {{ round($data['RequiredInstallationTorque'] ?? 0, 2) }} ft-lbs</p>
            </div>
        </div>
        <div>
            @if (count($this->getFilteredResults()) > 0)
                <table class="w-full bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-gray-950 dark:text-white">Depth (ft)</th>
                            <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-gray-950 dark:text-white">Ultimate Anchor Capacity (lbs)</th>
                            <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-gray-950 dark:text-white">Torsional Resistance (lb-ft)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($this->minDepth > 1)
                            <tr
                                class="cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5"
                                wire:click="expandAbove"
                            >
                                <td colspan="3" class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-center text-primary-600 dark:text-primary-400 font-medium">
                                    Expand Above
                                </td>
                            </tr>
                        @endif

                        @foreach ($this->getFilteredResults() as $depth => $results)
                            <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800">
                                @if ($depth > $data['ApproximatePileEmbedmentDepth'])
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-center text-primary-500 dark:text-primary-500">{{ $depth }}</td>
                                @else 
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-center text-success-500 dark:text-success-500">{{ $depth }}</td>
                                @endif
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-center text-gray-950 dark:text-white">{{ round($results['anchor_capacity'], 2) }} lbs</td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-center text-gray-950 dark:text-white">{{ round($results['torsional_resistance'], 2) }} lb-ft</td>
                            </tr>
                        @endforeach

                        @if ($this->maxDepth < max(array_keys($data['DepthResults'])))
                            <tr
                                class="cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5"
                                wire:click="expandBelow"
                            >
                                <td colspan="3" class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 text-center text-primary-600 dark:text-primary-400 font-medium">
                                    Expand Below
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            @else
                <p class="dark:text-gray-400">No depth-specific results available.</p>
            @endif
            @php
                $pileHeadPosition = (float)($this->data['PileHeadPosition'] ?? 0);
                $maxDepth = (float)($this->data['MaxDepth'] ?? 0);
                $anchorDeclinationDegree = (float)($this->data['AnchorDeclinationDegree'] ?? 0);
                $waterTableDepth = (float)($this->data['WaterTableDepth'] ?? 0);
                $soilLayers = $this->data['SoilLayers'] ?? collect([]);

                $declinationRad = deg2rad($anchorDeclinationDegree);
                $depthDifference = (float)($maxDepth - $pileHeadPosition);
                $anchorX = $depthDifference * cos($declinationRad);
                $anchorY = $pileHeadPosition + ($depthDifference * sin($declinationRad));

                $anchorX = is_numeric($anchorX) && !is_nan($anchorX) && !is_infinite($anchorX) ? (float)$anchorX : 0.0;
                $anchorY = is_numeric($anchorY) && !is_nan($anchorY) && !is_infinite($anchorY) ? (float)$anchorY : 0.0;

                $anchor = [
                    [0.0, $pileHeadPosition],
                    [(float)$anchorX, (float)$anchorY]
                ];

                $depthValues = array_values(array_reverse(
                    array_unique(
                        array_reverse(
                            array_merge(
                                array_map(fn($r) => (float)(($r['start_depth'] ?? 0) + $pileHeadPosition), $soilLayers->toArray() ?? []),
                                [(float)$maxDepth, (float)$waterTableDepth]
                            )
                        )
                    )
                ));
            @endphp
        </div>
        <div class="">
            @livewire(App\Filament\Resources\ProjectResource\Widgets\SoilProfileChart::class, [
                    'title' => 'Soil Layers',
                    'anchor' => $anchor,
                    'depth' => $depthValues,
                    'angle' => [$this->data['AnchorDeclinationDegree']]
                ])
                &nbsp;
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
                    const canvases = document.querySelectorAll('canvas');
                    
                    if (canvases.length >= 2) {
                        const chartImage1 = canvases[0].toDataURL('image/png');
                        const chartImage2 = canvases[1].toDataURL('image/png');
                        window.Livewire.dispatch('saveSoilChartImage', {base64Image: chartImage1});
                        window.Livewire.dispatch('saveResultChartImage', {base64Image: chartImage2});
                    } 
                    else {
                        alert("Canvas not found!");
                    }
                }, 1000);
            });
        </script>
    @endpush
</x-filament::page>