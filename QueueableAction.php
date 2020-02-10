<?php

namespace Spatie\QueueableAction;

trait QueueableAction
{
    /**
     * @return static
     */
    public function onQueue(?string $queue = null)
    {
        /** @var self $class */
        $class = new class($this, $queue) {
            protected $action;
            protected $queue;

            public function __construct(object $action, ?string $queue)
            {
                $this->action = $action;
                $this->onQueue($queue);
            }

            public function execute(...$parameters)
            {
                return dispatch(new ActionJob($this->action, $parameters))
                    ->onQueue($this->queue);
            }

            public function executeAfterResponse(...$parameters)
            {
                return dispatchAfterResponse(new ActionJob($this->action, $parameters))
                    ->onQueue($this->queue);
            }

            protected function onQueue(?string $queue): void
            {
                if (is_string($queue)) {
                    $this->queue = $queue;

                    return;
                }

                if (isset($this->action->queue)) {
                    $this->queue = $this->action->queue;
                }
            }
        };

        return $class;
    }

    public function tags(): array
    {
        return ['action_job'];
    }
}
