<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

namespace App\Utils;


class StringUtil
{
    public static function uniqrandom($limit)
    {
        return substr(
            base_convert(
                sha1(uniqid(mt_rand())),
                16,
                36),
            0, $limit);
    }
}