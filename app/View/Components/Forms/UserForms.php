<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserForms extends Component
{
    public $formComponents, $formConfig;
    /**
     * Create a new component instance.
     */
    public function __construct($formComponents, $formConfig)
    {
        $this->formComponents = $formComponents;
        $this->formConfig =  $formConfig;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.user-forms');
    }
}
