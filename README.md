<p align="center"><img src="https://images.mucts.com/image/exp_def_white.png" width="400"></p>
<p align="center">
    <a href="https://scrutinizer-ci.com/g/mucts/money"><img src="https://scrutinizer-ci.com/g/mucts/money/badges/build.png" alt="Build Status"></a>
    <a href="https://scrutinizer-ci.com/g/mucts/money"><img src="https://scrutinizer-ci.com/g/mucts/money/badges/code-intelligence.svg" alt="Code Intelligence Status"></a>
    <a href="https://scrutinizer-ci.com/g/mucts/money"><img src="https://scrutinizer-ci.com/g/mucts/money/badges/quality-score.png" alt="Scrutinizer Code Quality"></a>
    <a href="https://packagist.org/packages/mucts/money"><img src="https://poser.pugx.org/mucts/money/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/mucts/money"><img src="https://poser.pugx.org/mucts/money/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/mucts/money"><img src="https://poser.pugx.org/mucts/money/license.svg" alt="License"></a>
</p>

# Money
> Numeric amount case conversion.

## Installation

### Server Requirements
>you will need to make sure your server meets the following requirements:

- `php ^7.2`
- `GMP PHP Extension`
- `BCMath PHP Extension`
- `MBString PHP Extension`
- `mucts/helpers >=0.1.7`

~~~shell
composer require mucts/money
~~~

## Usage

- Arabic numeral amount into Chinese capital 

~~~php
<?php
$amount = '￥987654321.01';
// $amount = -12344555.67;
$amountCn = amount_to_cn($amount);
~~~

- Convert the amount in Chinese capital into Arabic numerals

~~~php
<?php
$amount = amount_to_digit($amountCn);
~~~

## License
> MIT
