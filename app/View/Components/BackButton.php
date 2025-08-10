<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BackButton extends Component
{

    public $fallback;

    public function __construct($fallback = 'admin.dashboard') {

        $this->fallback = $fallback;
    }
    
    public function render(): View|Closure|string
    {
        return view('components.back-button');
    }
}
