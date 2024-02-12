<?php

namespace app\services;

abstract class Helper
{
    const BASE_NAMESPACE = 'app';
    const SRC_CONTROLLERS = 'controllers';
    const SRC_MODELS = 'models';
    const SRC_VIEW = 'views';

    static public function getPathRoot(): string
    {
        return '/' . trim($_SERVER['DOCUMENT_ROOT'], '/');
    }

    static public function getPathSrc(): string
    {
        return self::getPathRoot() . '/src';
    }

    static public function getPathStorage(): string
    {
        return self::getPathRoot() . '/storage';
    }

    static public function getController(...$address): string
    {
        return self::getAddress('\\', self::BASE_NAMESPACE, self::SRC_CONTROLLERS, $address);
    }

    static public function getModel(...$address): string
    {
        return self::getAddress('\\', self::BASE_NAMESPACE, self::SRC_MODELS, $address);
    }

    static public function getView(...$address): string
    {
        return '/' . self::getAddress('/', self::getPathSrc(), self::SRC_VIEW, $address);
    }

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
}