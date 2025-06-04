<?php

namespace App\View\Components\Modals;

use Illuminate\View\Component;

class SearchForm extends Component
{
    public $title;
    public $isSearchModalOpen;

    public function __construct($title = null, $isSearchModalOpen = false)
    {
        $this->title = $title;
        $this->isSearchModalOpen = $isSearchModalOpen;
    }

    public function render()
    {
        return view('components.modals.search-form');
    }
}