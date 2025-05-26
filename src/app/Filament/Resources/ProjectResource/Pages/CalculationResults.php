<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Services\PDFResult;
use Filament\Actions\Action;
use App\Services\ResultService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Route;
use App\Filament\Resources\ProjectResource;

class CalculationResults extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.resources.project-resource.pages.calculation-results';

    public ?array $data = [];
    public ?int $projectId = null;

    // State variables for the depth range
    public int $minDepth = 1;
    public int $maxDepth = 100;

    public function mount(): void
    {
        // Get the project ID from the URL
        $this->projectId = Route::current()->parameter('record');

        // Instantiate the ResultService
        $ResultService = app(ResultService::class);

        try {
            // Call the calculateProjectResults method
            $anchorResult = $ResultService->calculateProjectResults($this->projectId);

            // Store the result in the $data property
            $this->data = $anchorResult->toArray();

            // Initialize the depth range based on the approximate embedment depth
            $this->initializeDepthRange();
        } catch (\Exception $e) {
            // Handle the exception (e.g., display an error message)
            session()->flash('error', $e->getMessage());
            $this->data = [];
        }
    }

    protected function initializeDepthRange(): void
    {
        // Get the approximate embedment depth
        $approximateDepth = $this->data['ApproximatePileEmbedmentDepth'] ?? 0;

        // Set the initial range (10 above and 10 below)
        $this->minDepth = max(1, $approximateDepth - 10);
        $this->maxDepth = $approximateDepth + 10;
    }

    public function expandAbove(): void
    {
        // Expand the range above
        $this->minDepth = max(1, $this->minDepth - 10);
    }

    public function expandBelow(): void
    {
        // Expand the range below
        $this->maxDepth += 10;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recalculate')
                ->label('Recalculate')
                ->action(function () {
                    // Instantiate the ResultService
                    $ResultService = app(ResultService::class);

                    try {
                        // Call the calculateProjectResults method
                        $anchorResult = $ResultService->calculateProjectResults($this->projectId);

                        // Store the result in the $data property
                        $this->data = $anchorResult->toArray();
                        $this->initializeDepthRange(); // Reinitialize the depth range
                        $this->dispatch('recalculated');
                        $this->dispatch('notify', [
                            'type' => 'success',
                            'message' => 'Calculation Recalculated',
                        ]);
                    } catch (\Exception $e) {
                        // Handle the exception (e.g., display an error message)
                        session()->flash('error', $e->getMessage());
                        $this->data = [];
                    }
                }),
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    if (empty($this->data)) {
                        $this->dispatch('notify', [
                            'type' => 'danger',
                            'message' => 'No data available to export',
                        ]);
                        return;
                    }
                    
                    // Use the PDF service with array data
                    $pdfService = app(PDFResult::class);
                    return $pdfService->generatePdf($this->data);
                })
                ->hidden(fn () => empty($this->data)),
        ];
    }

    public function getFilteredResults(): array
    {
        if (empty($this->data['DepthResults'])) {
            return [];
        }

        // Filter the results based on the current depth range
        return array_filter($this->data['DepthResults'], function($depth) {
            return $depth >= $this->minDepth && $depth <= $this->maxDepth;
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getTitle(): string
    {
        return isset($this->data['CalculationType']) ? "{$this->data['CalculationType']->value} Results" : 'Calculation Results';
    }
}