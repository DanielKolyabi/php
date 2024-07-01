<?php

namespace App\Admin\Widgets;

use App\Models\Product;
use Arrilot\Widgets\AbstractWidget;

class ProductsWidget extends AbstractWidget
{
    protected $config = [];

    public function run(
    ): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $count = Product::count();
        return view(
            'voyager::dimmer',
            array_merge($this->config, [
                'icon' => 'voyager-bar-chart',
                'title' => __('Продукты'),
                'text' => __('Количество продуктов') . ": $count",
                'button' => [
                    'text' => __('Перейти к списку'),
                    // 'link' => route('voyager.products.index'),
                    'link' => '/admin/products',
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
