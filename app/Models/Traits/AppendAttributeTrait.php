<?php

namespace App\Models\Traits;

/**
 * Author jehan.afwazi@gmail.com.
 */

trait AppendAttributeTrait
{

    public static $withoutAppends = false;

    /**
     * Check if $withoutAppends is enabled.
     *
     * @return array
     */
    protected function getArrayableAppends()
    {
        if (self::$withoutAppends) {
            return [];
        }
        return parent::getArrayableAppends();
    }

}