<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\View\Components\Render;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        protected Render $render
    ) {
        $this->render = new Render('Employee', 'components.employee');
    }

    public function getRequestData(Request $request): object
    {
        if ($request->isJson()) {
            return (object)$request->json()->all();
        }
        return (object)$request->input();
    }

    public function index(): View|Closure|string
    {
        return $this->render->render();
    }

    public function store(Request $request): object
    {
        $data = $this->getRequestData($request);
        $employee = new Employee();
        foreach ($data as $key => $value) {
            if (stripos($key, 'token') !== false) {
                continue;
            }
            if (stripos($key, 'workData') !== false) {
                $employee->$key = $this->getWorkData($value);
                continue;
            }
            $employee->$key = $value;
        }
        // $employee->save(); // TODO добавить сохранение
        return $this->render->render('store', (object)$employee->getAttributes());
    }

    public function update(Request $request, int $id): bool
    {
        $employee = new Employee(); // TODO ??? Employee->findOne(['id' => $id]);
        // $employee = Employee::query()->where('id', '=', $id)->take(1)->get();
        if ($employee) {
            $data = $this->getRequestData($request);
            foreach ($data as $key => $value) {
                if (stripos($key, 'token') !== false) {
                    continue;
                }
                if (stripos($key, 'workData') !== false) {
                    $employee->$key = $this->getWorkData($value);
                    continue;
                }
                $employee->$key = $value;
            }
            return true; // return $employee->save(); // TODO добавить обновление
        }
        return false;
    }

    public function getPath(Request $request): string
    {
        return $request->path();
    }

    public function getUrl(Request $request): string
    {
        return $request->url();
    }

    public function getWorkData(string $address): string
    {
        // Пример
        $response = json_decode($address, true) ?? [];
        $address = [
            'zipcode' => $response['address']['zipcode'] ?? '',
            'city' => $response['address']['city'] ?? '',
            'street' => $response['address']['street'] ?? '',
            'suite' => $response['address']['suite'] ?? '',
        ];
        return implode(', ', $address);
    }
}
