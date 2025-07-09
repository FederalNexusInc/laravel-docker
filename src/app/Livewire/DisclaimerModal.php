<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DisclaimerModal extends Component
{
    public $showModal = false;

    public function mount()
    {
        $this->showModal = Auth::user()->disclaimer_acknowledged == 0;
    }

    public function acknowledge()
    {
        Auth::user()->update(['disclaimer_acknowledged' => 1]);
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.disclaimer-modal');
    }
}
