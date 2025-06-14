<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Models\Project;
use App\Services\PDFResult;
use Filament\Actions\Action;
use App\Services\ResultService;
// use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Route;
use App\Filament\Resources\ProjectResource;
use Illuminate\Database\Eloquent\Collection;

class CalculationResults extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.resources.project-resource.pages.calculation-results';

    public ?array $data = [];
    public ?int $projectId = null;
    // public $anchors = [];
    public Collection $anchors;
    public ?int $selectedAnchorId = null;

    // State variables for the depth range
    public int $minDepth = 1;
    public int $maxDepth = 100;
    
    public function mount(): void
    {
        // Get the project ID from the URL
        $this->projectId = Route::current()->parameter('record');
        $project = Project::find($this->projectId);
        $this->anchors = $project->anchors;

        // Set the first anchor as the default selected one if available
        if ($this->anchors->isNotEmpty()) {
            $this->selectedAnchorId = $this->anchors->first()->anchor_id;
        } else {
            // If no anchors, ensure selectedAnchorId is null and handle gracefully
            $this->selectedAnchorId = null;
            session()->flash('error', 'No anchors found for this project.');
        }

        // Call recalculate to perform the initial calculation for the default or selected anchor
        $this->recalculate(); // This will use $this->selectedAnchorId
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

    // This method will now be called when the selectedAnchorId changes (due to wire:model.live)
    // and also when the recalculate action button is clicked.
    public function recalculate(?int $anchorId = null): void // <--- Make it accept an optional ID
    {
        // If an anchorId is passed (from a button click or direct call), use it.
        // Otherwise, use the currently selectedAnchorId property.
        $this->selectedAnchorId = $anchorId ?? $this->selectedAnchorId;

        // Only attempt calculation if an anchor is selected
        if (is_null($this->selectedAnchorId)) {
            session()->flash('error', 'Please select an anchor to perform calculations.');
            $this->data = [];
            return;
        }

        $ResultService = app(ResultService::class);

        try {
            // Pass the selectedAnchorId to your service
            $anchorResult = $ResultService->calculateProjectResults($this->projectId, 1, $this->selectedAnchorId);

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
    }

    // Livewire hook: this method will be automatically called when selectedAnchorId changes
    public function updatedSelectedAnchorId(): void
    {
        $this->recalculate(); // Simply call the main recalculate method
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Anchor changed, recalculating...',
        ]);
    }
}