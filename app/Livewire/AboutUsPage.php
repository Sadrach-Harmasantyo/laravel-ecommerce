<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('About Us - TokoBajuSepatu')]
class AboutUsPage extends Component
{
    public function render()
    {
        return view('livewire.about-us-page');
    }
}