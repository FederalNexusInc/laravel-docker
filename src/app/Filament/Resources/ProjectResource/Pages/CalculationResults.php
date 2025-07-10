<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Models\Project;
use App\Services\PDFResult;
use Filament\Actions\Action;
use App\Services\ResultService;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ProjectResource;
use Illuminate\Database\Eloquent\Collection;

class CalculationResults extends Page
{
    protected static ?string $navigationIcon = 'clarity-document-outline-badged';
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.resources.project-resource.pages.calculation-results';

    public ?array $data = [];
    public ?int $projectId = null;
    public ?Project $project;
    public Collection $anchors;
    public ?int $selectedAnchorId = null;

    public int $minDepth = 1;
    public int $maxDepth = 100;

    public function mount(): void
    {
        $this->projectId = Route::current()->parameter('record');
        $this->project = Project::find($this->projectId);
        $this->anchors = $this->project->anchors;

        if ($this->anchors->isNotEmpty()) {
            $this->selectedAnchorId = $this->anchors->first()->anchor_id;
        } else {
            $this->selectedAnchorId = null;
            session()->flash('error', 'No anchors found for this project.');
        }

        $this->recalculate(); // This will use $this->selectedAnchorId
    }

    protected function initializeDepthRange(): void
    {
        $approximateDepth = $this->data['ApproximatePileEmbedmentDepth'] ?? 0;

        $this->minDepth = max(1, $approximateDepth - 10);
        $this->maxDepth = $approximateDepth + 10;
    }

    public function expandAbove(): void
    {
        $this->minDepth = max(1, $this->minDepth - 10);
    }

    public function expandBelow(): void
    {
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

        return array_filter($this->data['DepthResults'], function($depth) {
            return $depth >= $this->minDepth && $depth <= $this->maxDepth;
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getTitle(): string
    {
        return isset($this->data['CalculationType']) ? "{$this->data['CalculationType']->value} Results" : 'Calculation Results';
    }

    public function recalculate(?int $anchorId = null): void // <--- Make it accept an optional ID
    {
        $this->selectedAnchorId = $anchorId ?? $this->selectedAnchorId;

        if (is_null($this->selectedAnchorId)) {
            session()->flash('error', 'Please select an anchor to perform calculations.');
            $this->data = [];
            return;
        }

        $ResultService = app(ResultService::class);

        try {
            $anchorResult = $ResultService->calculateProjectResults($this->projectId, 1, $this->selectedAnchorId);

            $this->data = $anchorResult->toArray();
            $this->initializeDepthRange(); // Reinitialize the depth range

            $this->dispatch('recalculated');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Calculation Recalculated',
            ]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            $this->data = [];
        }
    }

    public function updatedSelectedAnchorId(): void
    {
        $this->recalculate(); // Simply call the main recalculate method
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Anchor changed, recalculating...',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getSubNavigationParameters(): array
    {
        return [
            'record' => Project::find($this->projectId),
        ];
    }

    public function getSubNavigation(): array
    {
        return static::getResource()::getRecordSubNavigation($this);
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

        $newBreadcrumbs = array_slice( $breadcrumbs, 0, 1 ) + [ 0 => "{$this->project->project_name}" ] + $breadcrumbs;

        return $newBreadcrumbs;
    }
}
