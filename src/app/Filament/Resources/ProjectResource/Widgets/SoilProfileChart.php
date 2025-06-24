<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class SoilProfileChart extends ChartWidget
{
    protected static ?string $heading = '';

    public string $title = 'Soil Layers';
    public array $depth = [30];
    public array $anchor = [];
    public array $angle = [30];
    public array $axis_x = [0, 30];

    protected $listeners = ['saveSoilChartImage' => 'saveBase64DecodedData'];

    public function mount($title = 'Soil Layers', $anchor = [], $depth = [30], $angle = [30]): void
    {
        $this->title = $title;
        $this->anchor = $anchor;
        $this->depth = $depth;
        $this->angle = $angle;
        $this->axis_x = [
            max($this->angle) === 90 ? (-1 * max($this->depth ?? [0])) / 2 : 0, 
            max($this->angle) === 90 ? (max($this->depth ?? [0]) / 2) : max($this->depth ?? [0])
        ];
    }

    public function getHeight(): string
    {
        return '250px';
    }

    protected function getData(): array
    {
        // Combine all soil layers into a single dataset with gaps
        $soilLayerPoints = [];
        foreach (array_slice($this->depth ?? [], 0, -1) as $depth) {
            $soilLayerPoints[] = [$this->axis_x[0], $depth];
            $soilLayerPoints[] = [$this->axis_x[1], $depth];
            $soilLayerPoints[] = [null, null]; // Creates a gap between lines
        }

        return [
            'datasets' => [
                [
                    'label' => 'Soil Layers',
                    'data' => $soilLayerPoints,
                    'borderColor' => 'yellow',
                    'borderWidth' => 4,
                    'fill' => false,
                    'tension' => 0,
                    'pointRadius' => 0,
                    'segment' => [
                        'borderColor' => 'yellow'
                    ],
                ],
                [
                    'label' => 'Anchor Position',
                    'data' => $this->anchor,
                    'borderColor' => '#1B90FD',
                    'borderWidth' => 6,
                    'fill' => false,
                    'tension' => 0,
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Water Table Depth',
                    'data' => [
                        [$this->axis_x[0], end($this->depth)], 
                        [$this->axis_x[1], end($this->depth)]
                    ],
                    'borderColor' => '#ABC670',
                    'borderWidth' => 4,
                    'fill' => false,
                    'tension' => 0,
                    'pointRadius' => 0,
                ]
            ]
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'aspectRatio' => 1,
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => $this->title,
                ],
                'legend' => [
                    'display' => true,
                    'onClick' => $this->getLegendClickHandler(),
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Depth (ft)',
                    ],
                    'min' => 0,
                    'max' => max($this->depth),
                    'reverse' => true,
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => true,
                    ],
                    'beginAtZero' => true,
                ],
                'x' => [
                    'display' => false,
                    'type' => 'linear',
                    'min' => $this->axis_x[0],
                    'max' => $this->axis_x[1],
                ],
            ],
        ];
    }

    protected function getLegendClickHandler(): string
    {
        return <<<JS
            function(evt, legendItem, legend) {
                const chart = this.chart || legend.chart;
                const meta = chart.getDatasetMeta(legendItem.datasetIndex);
                
                // Toggle visibility
                meta.hidden = !meta.hidden;
                
                // Update the dataset's hidden property for consistency
                chart.data.datasets[legendItem.datasetIndex].hidden = meta.hidden;
                
                chart.update();
            }
        JS;
    }

    #[On('saveSoilChartImage')]
    public function saveSoilChartImage($base64Image)
    {
        session(['soil_chart_image_base64' => $base64Image]);
    }
}