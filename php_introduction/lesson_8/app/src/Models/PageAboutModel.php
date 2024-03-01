<?php

namespace Root\App\Models;

class PageAboutModel extends BaseModel
{
    static protected function getTableName(): string
    {
        return '';
    }
    
    static protected function getUniqueField(): string
    {
        return '';
    }
    
    public string $phone = '+7 (999) 999 99-99';
    public string $address = 'г. Москва, ул. Придуманная 1';
    public string $timeZone = 'Europe/Moscow';
    public array $workHours = [
        'пн' => '09:00 - 18:00',
        'вт' => '09:00 - 18:00',
        'ср' => '09:00 - 18:00',
        'чт' => '09:00 - 18:00',
        'пт' => '09:00 - 18:00',
        'сб' => 'выходной',
        'вс' => 'выходной',
    ];
}