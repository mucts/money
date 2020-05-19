# Money
> Numeric amount case conversion

[![Build Status](https://scrutinizer-ci.com/g/mucts/money/badges/build.png)](https://scrutinizer-ci.com/g/mucts/money)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/mucts/money/badges/code-intelligence.svg)](https://scrutinizer-ci.com/g/mucts/money)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mucts/money/badges/quality-score.png)](https://scrutinizer-ci.com/g/mucts/money)
[![Latest Stable Version](https://poser.pugx.org/mucts/money/v/stable.svg)](https://packagist.org/packages/mucts/money) 
[![Total Downloads](https://poser.pugx.org/mucts/money/downloads.svg)](https://packagist.org/packages/mucts/money) 
[![Latest Unstable Version](https://poser.pugx.org/mucts/money/v/unstable.svg)](https://packagist.org/packages/mucts/money) 
[![License](https://poser.pugx.org/mucts/money/license.svg)](https://packagist.org/packages/mucts/money)

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
