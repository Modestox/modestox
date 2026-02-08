<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Modules\Modestox\Home\Controller;

use Core\Http\Routing\Contracts\ControllerInterface;
use Core\Language\Contracts\TranslatorInterface;

/**
 * Class Index
 * Demonstrates Dependency Injection and Translation mechanism.
 */
readonly class Index implements ControllerInterface
{
    /**
     * Constructor injection of the Translator service.
     */
    public function __construct(
        private TranslatorInterface $translator
    ) {}

    /**
     * Executes the main controller logic.
     */
    public function execute(): void
    {
        // Using translation with placeholder
        $title = $this->translator->translate('Welcome to Modestox!');
        $greeting = $this->translator->translate('Hello, :name!', [':name' => 'Sergey']);

        echo "<div style='font-family: sans-serif; padding: 50px; text-align: center;'>";
        echo "<h1 style='color: #2c3e50;'>{$title}</h1>";
        echo "<p style='color: #7f8c8d; font-size: 1.2em;'>{$greeting}</p>";
        echo "</div>";
    }
}