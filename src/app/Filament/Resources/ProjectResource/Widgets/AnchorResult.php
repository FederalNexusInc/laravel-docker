<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class AnchorResult extends ChartWidget
{
    protected static ?string $heading = '';

    public string $title = '';
    public array $depth = [];
    public array $anchorCapacity = [];
    public array $torsionalResistance = [];
    
    protected $listeners = ['saveResultChartImage' => 'saveBase64DecodedData'];

    public function getFilteredResults()
    {
        $results = []; // however you gather your data

        // After collecting your results
        $results = array_unique($results, SORT_REGULAR); // Removes duplicate values
        ksort($results); // Sort by depth (key)

        return $results;
    }

    public function mount($title = '', $depth = [], $anchorCapacity = [], $torsionalResistance = []): void
    {
        $this->title = $title . ' Chart';
        $result_anchorCapacity = $anchorCapacity;
        $result_torsionalResistance = $torsionalResistance;
        $this->depth = array_unique(array_keys($this->getFilteredResults()));

        $this->anchorCapacity = array_map(fn($value) => is_numeric($value) ? $value / 1000 : 0, $result_anchorCapacity);
        $this->torsionalResistance = $result_torsionalResistance;
    }

    #[On('saveResultChartImage')]
    public function saveResultChartImage($base64Image)
    {
        session(['result_chart_image_base64' => $base64Image]); // Store in session
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Anchor Capacity',
                    'data' => $this->anchorCapacity,
                    'borderColor' => '#0000FF', // Blue
                    'backgroundColor' => 'transparent',
                    'yAxisID' => 'y',
                    'fill' => false,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Torsional Resistance',
                    'data' => $this->torsionalResistance,
                    'borderColor' => '#00FF00', // Green
                    'backgroundColor' => 'transparent',
                    'yAxisID' => 'y1',
                    'fill' => false,
                    'tension' => 0.5,
                ],
            ],
            'labels' => $this->depth,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Capacity (Kip)',
                    ],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Torsional (lb-ft)',
                    ],
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => $this->title,
                ],
            ],
        ];
    }
}