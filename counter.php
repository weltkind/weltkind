<?php

/**
 * Начальный код
 *
 * Проблемы:
 * При записи в файл несколькими процессами они будут друг друга перезаписывать
 * Аналогичная проблема при чтении, между чтением и записью, состояние инкремента может изменяться
 */

//file_put_contents("./counter.txt", file_get_contents("./counter.txt") + 1);

/**
 * Из-за проблемы с чтением, решить задачу добавлением третьего аргумента в file_put_contents LOCK_EX не получится.
 * Придется ударяться во все тяжкие. Почти в самое С.
 */

$filename = './counter.txt';
$fp = fopen( $filename, "c+");
flock($fp, LOCK_EX);
$count = (int)fread($fp, filesize($filename))+1;
rewind($fp);
fputs($fp,  $count);
flock($fp, LOCK_UN);
fclose($fp);
