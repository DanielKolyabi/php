<?php

namespace App\Http\Controllers;

use App\View\Components\Render;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FormProcessor extends Controller
{
    public function __construct(
        protected Render $userform
    ) {
        $this->userform = new Render('UserForm', 'components.userform');
    }

    public function index(): View|Closure|string
    {
        return $this->userform->render();
    }

    public function store(Request $request): object
    {
        $response = (object)[];
        foreach ($request->keys() as $key) {
            if (stripos($key, 'token') === false) {
                $response->$key = $request[$key];
            }
        }
        return $this->userform->render('store', $response);
    }
}
