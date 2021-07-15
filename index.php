<?php

use Staple\Staple;
use Staple\LocateFiles;
use Staple\Builder;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/helpers/compat.php';

$builder = new Builder(new Staple);
$builder->build();