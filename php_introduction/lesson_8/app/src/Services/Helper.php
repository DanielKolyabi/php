<?php

namespace Root\App\Services;

abstract class Helper
{
    const ROOT_NAMESPACE = 'Root\App';
    const FOLDER_STORAGE = '/web/storage';
    const FOLDER_VIEWS = '/src/Views';
    
    static protected function getAddress($separator = '\\', ...$address): string
    {
        $adr = [];
        foreach ($address as $item) {
            if (!empty($item)) {
                $adr[] = !is_array($item) ? trim($item, $separator) : self::getAddress($separator, ...$item);
            }
        }
        return trim(implode($separator, $adr), $separator);
    }
    
    static public function getRootNamespace(...$address): string
    {
        return self::getAddress('\\', self::ROOT_NAMESPACE, $address);
    }
    
    static public function getRootPath(...$address): string
    {
        return '/' . self::getAddress('/', $_SERVER['DOCUMENT_ROOT'], $address);
    }
    
    static public function getController(...$address): string
    {
        return self::getRootNamespace('controllers', ...$address);
    }
    
    static public function getModel(...$address): string
    {
        return self::getRootNamespace('models', ...$address);
    }
    
    static public function getViewPath(...$address): string
    {
        return self::getRootPath(self::FOLDER_VIEWS, ...$address);
    }
    
    static public function getStoragePath(...$address): string
    {
        return self::getRootPath(self::FOLDER_STORAGE, ...$address);
    }
}