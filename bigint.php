<?php
$a = 9223372036854775807;
$b = 2;

$sum = gmp_add($a, $b);
echo gmp_strval($sum);
