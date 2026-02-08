<?php

/**
 * Modestox CMS - Entry Point
 */

declare(strict_types=1);

// 1. Подключаем автозагрузчик Composer
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Запускаем приложение
use Core\Bootstrap\Application;

$app = new Application();
$app->run();