<?php

namespace App\Ai\Tools;

use App\Models\AgentObservation;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ReportObservation implements Tool
{
    public function __construct(
        protected string $agentConfigId,
        protected string $userId,
        protected string $conversationId,
    ) {}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Rapporteer opmerkelijke gebeurtenissen, samenvattingen of slimme vragen voor beoordeling door de docent.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        AgentObservation::create([
            'agent_config_id' => $this->agentConfigId,
            'user_id' => $this->userId,
            'conversation_id' => $this->conversationId,
            'category' => $request['category'] ?? null,
            'content' => (string) $request['content'],
        ]);

        return 'Observation recorded.';
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'category' => $schema
                ->string()
                ->max(50)
                ->description('Een korte tag om het type observatie aan te geven')
                ->required(),
            'content' => $schema
                ->string()
                ->max(10000)
                ->description('De inhoud van de observatie, zoals een samenvatting van een gebeurtenis, een slimme vraag, of een opmerkelijke interactie')
                ->required(),
        ];
    }
}
