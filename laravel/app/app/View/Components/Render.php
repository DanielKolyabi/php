<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Render extends Component
{
    public function __construct(
        public ?string $title = null,
        public string $template = 'layouts'
    )
    {
        //
    }

    public function render(string $subTemplate = 'index', object $props = null): View|Closure|string
    {
        return view('main.index', [
            'title' => $this->title,
            'body' => "$this->template.$subTemplate",
            'data' => $props !== null ? $props : (object)[]
        ]);
    }
}
