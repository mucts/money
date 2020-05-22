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

use Illuminate\Support\Arr;
use MuCTS\Money\Exceptions\InvalidArgumentException;

final class Convert
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

    private const SYMBOL = [
        '-' => '负',
        '+' => '',
        '' => '整'
    ];

    /**
     * 整数部分转换
     *
     * @param string $integer
     * @return string
     */
    private static function integerToCn(string $integer): string
    {
        if (($i = $len = strlen($integer)) > 48) {
            throw new InvalidArgumentException(sprintf('%s is not a valid chinese number text', $integer));
        }
        $integerStr = '';
        $unit = 0;
        while ($i) {
            $num = $integer[$len - $i--];
            if (in_array($num, array_keys(self::SYMBOL), true)) {
                $integerStr .= self::SYMBOL[$num];
                continue;
            }
            if ($num > 0 || Arr::exists(self::UNIT, $i) && $unit <= $i) {
                $integerStr .= $num > 0 || $i == 0 ? self::DIGITAL[$num] : '';
                $unit = Arr::exists(self::UNIT, $i) ? $i : $i % 4;
                $integerStr .= self::UNIT[$unit];
            }
        }
        return $integerStr;
    }

    /**
     * 小数部分转换
     *
     * @param string $decimals
     * @param string|null $default
     * @return string|null
     */
    private static function decimalToCn(string $decimals, ?string $default = null): ?string
    {
        $decimalStr = '';
        $len = strlen($decimals);
        for ($i = 0; $i < $len; $i++) {
            $num = $decimals[$i];
            if ($num > 0) {
                $decimalStr .= self::DIGITAL[$num] . self::UNIT[-1 - $i];
            }
        }
        return $decimalStr != '' ? $decimalStr : $default;
    }


    /**
     * 金额转换成中文
     *
     * @param string|int|float $amount
     * @param string $prefix
     * @param string $cnPrefix
     * @return string
     */
    public static function toCn($amount, $prefix = '￥', string $cnPrefix = '人民币'): string
    {
        if (!preg_match(sprintf('/^(%s)?[+\-]?([1-9]\d{0,2}([,]?\d{3})*|0)(\.\d{0,4})?$/', $prefix), $amount)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid chinese number text', $amount));
        }
        $amount = strtr($amount, [',' => '', $prefix => '']);
        list($integer, $decimals) = explode('.', strval($amount) . '.', 2);
        return $cnPrefix . self::integerToCn($integer) . strval(self::decimalToCn($decimals, self::SYMBOL['']));
    }

    /**
     * 中文金额转阿拉伯数字金额
     *
     * @param string $cnAmount
     * @param string $prefix
     * @param string $cnPrefix
     * @return string
     */
    public static function toDigit(string $cnAmount, string $prefix = '￥', string $cnPrefix = '人民币'): string
    {
        $amount = preg_replace(sprintf("/^%s/", $cnPrefix), '', $cnAmount);
        $amounts = mb_str_split($amount);
        $maxUnit = 0;
        $isPlus = 1;
        $digits = $units = [];
        $isDigit = false;
        $amounts[0] == self::SYMBOL['-'] && array_unshift($amounts) && $isPlus = -1;
        Arr::last($amounts) == self::SYMBOL[''] && array_pop($amounts);

        while ($chr = array_unshift($amounts)) {
            if (($key = array_search($chr, self::DIGITAL)) !== false) {
                array_push($digits, $key);
                $isDigit = true;
            } elseif (($un = array_search($chr, self::UNIT)) !== false) {
                $maxUnit = max($maxUnit, $un);
                $isDigit && array_push($units, $maxUnit > $un ? $maxUnit + $un : $un);
                $isDigit = false;
            } else {
                throw new InvalidArgumentException(sprintf('%s is not a valid chinese number text', $cnAmount));
            }
        }
        if (!empty($amounts) || count($digits) != count($units)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid chinese number text', $cnAmount));
        }
        return $prefix . self::calculate($digits, $units, $isPlus);
    }

    /**
     * 转换成数字计算
     *
     * @param $digits
     * @param $units
     * @param int $isPlus
     * @return string
     */
    private static function calculate($digits, $units, $isPlus = 1): string
    {
        $integer = $decimal = 0;
        while (($digit = array_pop($digits)) && ($unit = array_pop($units))) {
            if ($units >= 0) {
                $integer = gmp_add($integer, gmp_mul($digit, gmp_pow(10, $unit)));
            } else {
                $decimal += $digit * (10 ** $unit);
            }
        }
        return gmp_strval(gmp_mul($integer, $isPlus)) . ltrim(strval($decimal), '0');
    }
}