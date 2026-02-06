<?php
/**
 * Modestox CMS - Security-First E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

use Core\App\App;

// Подключаем автозагрузку
require_once __DIR__ . '/../vendor/autoload.php';

// Запускаем приложение
$app = new App();
$app->run();