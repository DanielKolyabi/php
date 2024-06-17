<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\View\Components\Render;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function __construct(
        protected Render $render
    ) {
        $this->render = new Render('Logs', 'components.logs');
    }

    public function index(Request $request, $page = 1): View|Closure|string
    {
        $result = ['error' => 'Logs not found'];
        $model = Log::all()->sortDesc()->skip(($page - 1) * 50)->take(50);
        if ($model) {
            $result = ['logs' => []];
            foreach ($model->getDictionary() as $value) {
                $result['logs'][] = $value->getAttributes();
            }
            $result['page'] = $page;
        }
        return $this->render->render('index', (object)($result));
    }
}
