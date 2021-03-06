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
        '' => '整',
        ',' => ''
    ];

    /**
     * 整数部分转换
     *
     * @param int[] $integers
     * @return string
     */
    private static function integerToCn(array $integers): string
    {
        $len = count($integers) - 1;
        return collect($integers)->filter(function ($int, $index) use ($len) {
            return $int > 0 || $index == $len || self::UNIT[$index];
        })->map(function ($int, $index) use ($len) {
            $index = $len - $index;
            return ($int > 0 || $len == 0 ? self::DIGITAL[$int] : '') . (Arr::exists(self::UNIT, $index)
                    ? Arr::get(self::UNIT, $index) : Arr::get(self::UNIT, $index % 4));
        })->implode('');
    }

    /**
     * 小数部分转换
     *
     * @param int[] $decimals
     * @param string|null $default
     * @return string|null
     */
    private static function decimalToCn(array $decimals, ?string $default = null): ?string
    {
        $result = collect($decimals)->filter(function ($int) {
            return $int > 0;
        })->map(function ($int, $index) {
            return self::DIGITAL[$int] . self::UNIT[-1 - $index];
        })->implode('');
        return empty($result) ? $default : $result;
    }

    /**
     * 获取金额大写前缀
     *
     * @param string $amount
     * @param string $cnPrefix
     * @return string
     */
    private static function getCnPrefix(string &$amount, string $cnPrefix): string
    {
        $cnPrefix .= Arr::get(self::SYMBOL, $amount[0], '');
        $amount = preg_replace(sprintf('/[%s]/', implode(array_keys(self::SYMBOL))), '', $amount);
        return $cnPrefix;
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
        $amount = strval($amount);
        if (!preg_match(sprintf('/^(%s)?[+\-]?([1-9]\d{0,2}([,]?\d{3}){0,15}|0)(\.\d{0,4})?$/', $prefix), $amount)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid amount number.', $amount));
        }
        $amount = preg_replace(sprintf('/^%s/', $prefix), '', $amount);
        $cnPrefix = self::getCnPrefix($amount, $cnPrefix);
        list($integer, $decimals) = explode('.', $amount . '.0', 2);
        return $cnPrefix . self::integerToCn(str_split($integer)) . strval(self::decimalToCn(str_split($decimals), self::SYMBOL['']));
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
        $cnAmount = preg_replace(sprintf("/^%s/", $cnPrefix), '', $cnAmount);
        if (empty($cnAmount)) {
            throw new InvalidArgumentException('this\'s not a valid chinese number text');
        }
        $amounts = mb_str_split($cnAmount);
        $isMinus = self::isMinus($amounts);
        Arr::last($amounts) == self::SYMBOL[''] && array_pop($amounts);
        list($digits, $units) = self::cnDecode($amounts);
        if (!empty($amounts) || count($digits) != count($units)) {
            throw new InvalidArgumentException('this\'s not a valid chinese number text');
        }
        return $prefix . self::calculate($digits, $units, $isMinus);
    }

    /**
     * 中文金额解析
     *
     * @param array $amounts
     * @return array[]
     */
    private static function cnDecode(array &$amounts)
    {
        $maxUnit = $unit = 0;
        $digits = $units = [];
        while ($chr = array_pop($amounts)) {
            if (($key = array_search($chr, self::DIGITAL)) !== false) {
                array_push($digits, $key);
                array_push($units, $unit);
            } elseif (($unit = array_search($chr, self::UNIT)) !== false) {
                $maxUnit = max($maxUnit, $unit);
                $unit = $maxUnit > $unit ? $maxUnit + $unit : $unit;
            } else {
                throw new InvalidArgumentException('This\'s not a valid chinese number text');
            }
        }
        return [$digits, $units];
    }

    /**
     * 判断是否是负数
     *
     * @param array $amounts
     * @return bool
     */
    private static function isMinus(array &$amounts): bool
    {
        return !empty($amounts) && $amounts[0] == self::SYMBOL['-'] && array_shift($amounts);
    }

    /**
     * 转换成数字计算
     *
     * @param array $digits
     * @param array $units
     * @param bool $isMinus
     * @return string
     */
    private static function calculate(array $digits, array $units, bool $isMinus = false): string
    {
        $integer = $decimal = 0;
        while (is_int($digit = array_pop($digits)) && is_int($unit = array_pop($units))) {
            if ($unit >= 0) {
                $integer = gmp_add($integer, gmp_mul($digit, gmp_pow(10, $unit)));
            } else {
                $decimal += $digit * (10 ** $unit);
            }
        }
        return gmp_strval(gmp_mul($integer, $isMinus ? -1 : 1)) . ltrim(strval($decimal), '0');
    }
}