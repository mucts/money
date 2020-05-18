<?php
/**
 * This file is part of the mucts.com.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 * @version 1.0
 * @author herry<yuandeng@aliyun.com>
 * @copyright © 2020 MuCTS.com All Rights Reserved.
 */

namespace MuCTS\Money;


class Util
{
    public static function mbLtrim($string, $trim_chars = '\s')
    {
        return preg_replace('/^[' . $trim_chars . ']*(.*?)$/u', '\\1', $string);
    }

    public static function mbRtrim($string, $trim_chars = '\s')
    {
        return preg_replace('/^(.*?)[' . $trim_chars . ']*$/u', '\\1', $string);
    }
}