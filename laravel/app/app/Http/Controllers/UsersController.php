<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\View\Components\Render;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct(
        protected Render $render
    ) {
        $this->render = new Render('Users', 'components.users');
    }

    /**
     * @throws AuthorizationException
     */
    public function index(Request $request, ?int $id = null): View|Closure|string
    {
        $this->authorize('view-any', User::class);
        $result = null;
        if ($id === null) {
            $model = User::all();
            if ($model) {
                $result = ['users' => []];
                foreach ($model->getDictionary() as $value) {
                    $result['users'][] = $value->getAttributes();
                }
            } else {
                $result = ['error' => 'Users not found'];
            }
        } else {
            $model = User::query()->where('id', $id)->first();
            $result = $model ? $model->getAttributes() : ['error' => 'User not found'];
        }
        return $this->render->render('index', (object)($result));
    }
}
