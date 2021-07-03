<?php

$val = "this sentence will repeat";
$count = 10;

while ($count > 0 ) {
	echo "\e[10;20H";
	echo "\e[31m" . $val . "  " . $count . "\e[m\n";
	sleep(1);
	$count--;
}

