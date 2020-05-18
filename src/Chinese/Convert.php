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

namespace MuCTS\Money\Chinese;


use InvalidArgumentException;

class Convert
{
    private const DIGITAL = [
        0 => '零',
        1 => '壹',
        2 => '贰',
        3 => '叁',
        4 => '肆',
        5 => '伍',
        6 => '陆',
        7 => '柒',
        8 => '捌',
        9 => '玖',
    ];

    private const UNIT = [
        -4 => '毫',
        -3 => '厘',
        -2 => '分',
        -1 => '角',
        0 => '元',
        1 => '拾',
        2 => '佰',
        3 => '仟',
        4 => '万',
        8 => '亿',
        12 => '兆',
        16 => '京',
        20 => '垓',
        24 => '杼',
        28 => '穰',
        32 => '沟',
        36 => '涧',
        40 => '正',
        44 => '载',
        48 => '极'
    ];

    /**
     * 金额转换成中文
     *
     * @param string|int|float $amount
     * @param string $unit
     * @return string
     */
    public static function toCn($amount, string $unit = '人民币'): string
    {
        if (!preg_match('/^([1-9][0-9]{0,2}([,]?[0-9]{3})*|0)(\.[0-9]{0,4})?$/', $amount)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid chinese number text', $amount));
        }
        list($integer, $decimals) = explode('.', strval($amount) . '.', 2);
        $integer = strtr($integer, [',' => '']);
        if (($len = strlen($integer)) > 48) {
            throw new InvalidArgumentException(sprintf('%s is not a valid chinese number text', $amount));
        }
        $integerStr = '';
        $i = $len;
        while ($i) {
            $num = $integer[$len - $i--];
            if ($num > 0) {
                $integerStr .= self::DIGITAL[$num];
                if (isset(self::UNIT[$i])) {
                    $integerStr .= self::UNIT[$i];
                } else {
                    $integerStr .= self::UNIT[$i % 4];
                    while ($i) {
                        $num = $integer[$len - $i--];
                        if ($num > 0) {
                            $integerStr .= self::DIGITAL[$num];
                            if (isset(self::UNIT[$i])) {
                                $integerStr .= self::UNIT[$i];
                                break;
                            } else {
                                $integerStr .= self::UNIT[$i % 4];
                            }
                        } elseif (isset(self::UNIT[$i])) {
                            $integerStr .= self::UNIT[$i];
                            break;
                        }
                    }
                }
            }
        }
        $decimalStr = '';
        $len = strlen($decimals);
        for ($i = 0; $i < $len; $i++) {
            $num = $decimals[$i];
            if ($num > 0) {
                $decimalStr .= self::DIGITAL[$num] . self::UNIT[-1 - $i];
            }
        }
        $integerStr = $integerStr != '' ? $integerStr : self::DIGITAL[0];
        $integerStr = strpos($integerStr, self::UNIT[0]) ? $integerStr : $integerStr . self::UNIT[0];
        return $unit . $integerStr . ($decimalStr != '' ? $decimalStr : '整');
    }

    public static function toDigit(string $amount): float
    {

    }
}