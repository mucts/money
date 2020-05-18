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

use MuCTS\Money\Chinese\Convert;

if (!function_exists('amount_to_cn')) {
    /**
     * 金额转换成中文
     *
     * @param string|int|float $amount
     * @param string $symbol
     * @param string $cnSymbol
     * @return string
     */
    function amount_to_cn($amount, string $symbol = '￥', string $cnSymbol = '人民币')
    {
        return Convert::toCn($amount, $symbol, $cnSymbol);
    }
}

if (!function_exists('amount_to_digit')) {
    /**
     * 金额转换成阿拉伯数字
     *
     * @param string|int|float $amount
     * @param string $symbol
     * @param string $cnSymbol
     * @return string
     */
    function amount_to_digit($amount, string $symbol = '￥', string $cnSymbol = '人民币')
    {
        return Convert::toDigit($amount, $symbol, $cnSymbol);
    }
}