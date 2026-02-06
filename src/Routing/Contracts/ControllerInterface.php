<?php

declare(strict_types=1);

namespace Core\Routing\Contracts;

interface ControllerInterface
{
    public function execute(): void;
}