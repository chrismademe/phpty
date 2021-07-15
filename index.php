<?php

use PHPty\PHPty;
use PHPty\LocateFiles;
use PHPty\Builder;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/helpers/compat.php';

$builder = new Builder(new PHPty);
$builder->build();