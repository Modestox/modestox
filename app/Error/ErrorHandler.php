<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Error;

use Core\Bootstrap\Config;
use Core\Error\Contracts\ErrorHandlerInterface;
use Core\Http\Exceptions\HttpException;
use Core\Log\Logger;
use Throwable;

/**
 * Class ErrorHandler
 * Advanced error handler that identifies the true source of exceptions.
 */
class ErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
            private readonly Logger $logger,
            private readonly Config $config,
    ) {}

    public function handle(Throwable $exception): void
    {
        // 1. Логируем в любом случае
        $this->logger->logException($exception);

        $code = ($exception instanceof HttpException)
                ? $exception->getStatusCode()
                : 500;

        http_response_code($code);

        // 2. В режиме Prod скрываем детали, если это не HttpException
        if (!$this->config->isDev()) {
            $this->renderFriendlyError($code);
            return;
        }

        $this->render($code, $exception);
    }

    private function renderFriendlyError(int $code): void
    {
        echo "<h1>Error $code</h1>";
        echo "<p>Something went wrong. Our team has been notified. Please try again later.</p>";
    }

    private function render(int $code, Throwable $e): void
    {
        // Находим реальное место "поломки"
        [$realFile, $realLine] = $this->getRealCulprit($e);

        $message = htmlspecialchars($e->getMessage());
        $color = match (true) {
            $code >= 500 && $code != 503 => '#e74c3c',
            $code === 503                => '#f39c12',
            $code === 404                => '#3498db',
            default                      => '#2c3e50'
        };

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Error <?= $code ?> | Modestox</title>
            <style>
                body {
                    font-family: 'Segoe UI', sans-serif;
                    background: #1a1a1a;
                    color: #ccc;
                    margin: 0;
                    padding: 20px;
                    line-height: 1.5;
                }
                .container {
                    max-width: 1100px;
                    margin: 0 auto;
                }
                .card {
                    background: #2d2d2d;
                    border-radius: 8px;
                    overflow: hidden;
                    border: 1px solid #444;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
                }
                .header {
                    background: <?= $color ?>;
                    padding: 30px;
                    color: #fff;
                }
                .header h1 {
                    margin: 0;
                    font-size: 26px;
                }
                .content {
                    padding: 30px;
                }
                .label {
                    color: #ffffff;
                    font-size: 12px;
                    text-transform: uppercase;
                    margin-bottom: 5px;
                }
                .file-path {
                    background: #222;
                    padding: 10px;
                    border-radius: 4px;
                    font-family: monospace;
                    font-size: 13px;
                    color: #e67e22;
                    border: 1px solid #333;
                    margin-top: 10px;
                }
                .code-window {
                    background: #1e1e1e;
                    border-radius: 6px;
                    border: 1px solid #444;
                    margin: 20px 0;
                    overflow: hidden;
                }
                .code-line {
                    display: flex;
                    font-family: 'Consolas', monospace;
                    font-size: 14px;
                }
                .line-num {
                    width: 45px;
                    background: #252525;
                    color: #555;
                    text-align: right;
                    padding-right: 12px;
                    border-right: 1px solid #333;
                }
                .line-content {
                    padding-left: 12px;
                    white-space: pre;
                }
                .line-error {
                    background: rgba(231, 76, 60, 0.2);
                    border-left: 4px solid #e74c3c;
                    color: #fff;
                }
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                    font-size: 13px;
                }
                .data-table td {
                    padding: 8px;
                    border: 1px solid #3d3d3d;
                }
                .data-table td:first-child {
                    width: 150px;
                    color: #888;
                    background: #333;
                }
                .code-503 .label {
                    color: #ffffff
                }
                pre {
                    background: #1e1e1e;
                    padding: 15px;
                    border-radius: 4px;
                    overflow: auto;
                    font-size: 12px;
                    color: #999;
                    border: 1px solid #444;
                }
                h3 {
                    border-bottom: 1px solid #444;
                    padding-bottom: 10px;
                    margin-top: 40px;
                    color: #fff;
                    font-weight: 400;
                }
            </style>
        </head>
        <body class="code-<?= $code ?>">
        <div class="container">
            <div class="card">
                <div class="header">
                    <div class="label">Exception thrown:</div>
                    <h1><?= $code ?> | <?= $message ?></h1>
                    <div class="file-path"><?= $realFile ?>:<strong><?= $realLine ?></strong></div>
                </div>

                <div class="content">
                    <div class="label">Code Context</div>
                    <div class="code-window">
                        <?= $this->renderCodeSnippet($realFile, $realLine) ?>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <div>
                            <h3>Request Information</h3>
                            <table class="data-table">
                                <tr>
                                    <td>Method</td>
                                    <td><?= $_SERVER['REQUEST_METHOD'] ?></td>
                                </tr>
                                <tr>
                                    <td>URL</td>
                                    <td><?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td><?= $_SERVER['REMOTE_ADDR'] ?></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <h3>Environment</h3>
                            <table class="data-table">
                                <tr>
                                    <td>PHP Version</td>
                                    <td><?= PHP_VERSION ?></td>
                                </tr>
                                <tr>
                                    <td>Memory Use</td>
                                    <td><?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB</td>
                                </tr>
                                <tr>
                                    <td>Server</td>
                                    <td><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($_POST)): ?>
                        <h3>POST Data</h3>
                        <pre><?= htmlspecialchars(print_r($_POST, true)) ?></pre>
                    <?php endif; ?>

                    <h3>Stack Trace</h3>
                    <pre><?= $e->getTraceAsString() ?></pre>
                </div>
            </div>
        </div>
        </body>
        </html>
        <?php
    }

    /**
     * Finds the real point of origin for the error.
     */
    private function getRealCulprit(Throwable $e): array
    {
        $trace = $e->getTrace();
        $file = $e->getFile();
        $line = $e->getLine();

        // Если ошибка произошла внутри статической фабрики HttpException,
        // берем следующий шаг в стеке, чтобы найти место вызова.
        if (str_contains($file, 'HttpException.php') && isset($trace[0]['file'])) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        }

        return [$file, $line];
    }

    private function renderCodeSnippet(string $file, int $line): string
    {
        if (!file_exists($file) || !is_readable($file)) {
            return '<div style="padding:20px">File not accessible.</div>';
        }

        $lines = file($file);
        $start = max(0, $line - 6);
        $end = min(count($lines), $line + 5);
        $output = '';

        for ($i = $start; $i < $end; $i++) {
            $num = $i + 1;
            $class = ($num === $line) ? 'line-error' : '';
            $output .= "<div class='code-line {$class}'><div class='line-num'>{$num}</div>";
            $output .= "<div class='line-content'>" . htmlspecialchars($lines[$i]) . "</div></div>";
        }
        return $output;
    }
}