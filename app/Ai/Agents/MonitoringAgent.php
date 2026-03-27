<?php

namespace App\Ai\Agents;

use App\Ai\Tools\ReportObservation;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

class MonitoringAgent implements Agent, HasTools
{
    use Promptable;

    public function __construct(
        private string $instructions,
        private string $agentConfigId,
        private string $userId,
        private string $conversationId,
    ) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return $this->instructions;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        // Pre-configure the ReportObservation tool usage via instructions; actual params are supplied by the model.
        return [
            new ReportObservation(
                agentConfigId: $this->agentConfigId,
                userId: $this->userId,
                conversationId: $this->conversationId,
            ),
        ];
    }
}
