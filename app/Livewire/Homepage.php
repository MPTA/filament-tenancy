<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.website')]
#[Title('Homepage - TripMaker')]
class Homepage extends Component
{
    public function render()
    {
        return view('livewire.homepage');
    }
}
