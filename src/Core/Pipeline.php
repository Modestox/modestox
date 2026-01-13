<?php
declare(strict_types=1);

namespace Modestox\Core;

/**
 * Class Pipeline
 * Processes a request through a series of middleware stages.
 */
class Pipeline
{
    private array $pipes = [];
    private mixed $passable;

    /**
     * Set the data being sent through the pipeline
     */
    public function send(mixed $passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    /**
     * Set the array of middleware classes
     */
    public function through(array $pipes): self
    {
        $this->pipes = $pipes;
        return $this;
    }

    /**
     * Run the pipeline with a final destination callback
     */
    public function then(callable $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->createSlice(),
            $destination
        );

        return $pipeline($this->passable);
    }

    /**
     * Creates a closure for the middleware stack
     * @return callable
     */
    private function createSlice(): callable
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (is_string($pipe) && class_exists($pipe)) {
                    $pipe = new $pipe();
                }

                if ($pipe instanceof MiddlewareInterface) {
                    return $pipe->handle($passable, $stack);
                }

                return $stack($passable);
            };
        };
    }
}