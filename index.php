<?php

use PHPty\PHPty;
use PHPty\LocateFiles;
use PHPty\Builder;

require_once 'vendor/autoload.php';
require_once 'src/helpers/compat.php';

$builder = new Builder(new PHPty);
$builder->build();