<?php

namespace App\Admin\Widgets;

use App\Models\Category;
use Arrilot\Widgets\AbstractWidget;

class CategoriesWidget extends AbstractWidget
{
    protected $config = [];

    public function run(
    ): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $count = Category::count();
        return view(
            'voyager::dimmer',
            array_merge($this->config, [
                'icon' => 'voyager-list',
                'title' => __('Категории'),
                'text' => __('Количество категорий') . ": $count",
                'button' => [
                    'text' => __('Перейти к списку'),
                    // 'link' => route('voyager.categories.index'),
                    'link' => '/admin/categories',
                ],
                'image' => '',
            ])
        );
    }

    public function shouldBeDisplayed(): bool
    {
        return true;
    }
}
