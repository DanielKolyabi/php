<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\View\Components\Render;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function __construct(
        protected Render $render
    ) {
        $this->render = new Render('Book', 'components.book');
    }

    public function index(): View|Closure|string
    {
        return $this->render->render();
    }

    public function store(Request $request): View|Closure|string
    {
        try {
            // Set data
            $data = $request->all();
            foreach ($data as $key => $value) {
                $data[$key] = trim($value);
            }
            // Validate
            Validator::make($data, [
                'title' => ['required', 'unique:books,title', 'max:255'],
                'author' => ['required', 'max:100'],
                'genre' => ['required'],
            ])->validate();
            // Save model
            $model = new Book($data);
            return $this->render->render(
                'store',
                (object)($model->save() ? $model->getAttributes() : ['error' => 'Database error'])
            );
        } catch (ValidationException $e) {
            return $this->render->render(
                'store',
                (object)['error' => $e->getMessage()]
            );
        }
    }
}
