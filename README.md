# Money
> Numeric amount case conversion

## Installation

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
