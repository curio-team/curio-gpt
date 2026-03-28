<?php

namespace App\Jobs;

use App\Ai\Agents\MonitoringAgent;
use App\Models\AgentConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunMonitoringAgent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $agentConfigId,
        public string $userId,
        public string $conversationId,
        public string $userMessage,
        public array $uploadedFiles = [],
    ) {}

    public function handle(): void
    {
        $config = AgentConfig::find($this->agentConfigId);

        if (! $config || ! $config->monitoring_is_enabled || empty($config->monitoring_instructions)) {
            return;
        }

        $instructions = <<<PROMPT
Je monitort een student die interactie heeft met de hoofdassistent. Je beoordeelt of het bericht van de student een opmerkelijke gebeurtenis is.
Als het bericht opmerkelijk is, gebruik dan de ReportObservation tool met de benodigde parameters.
Als het niet opmerkelijk is, reageer dan met een korte "Geen observatie".
Een bericht is opmerkelijk volgens deze beschrijving van de docent:
'''
{$config->monitoring_instructions}
'''
PROMPT;

        $filesSection = '';
        if (! empty($this->uploadedFiles)) {
            $list = collect($this->uploadedFiles)->map(fn ($n) => "- {$n}")->implode("\n");
            $filesSection = "\nBijgevoegde bestanden:\n{$list}\n";
        }

        $prompt = <<<PROMPT
Student bericht:
'''
{$this->userMessage}
'''

{$filesSection}
PROMPT;

        $agent = new MonitoringAgent($instructions, $config->id, $this->userId, $this->conversationId);

        $model = $config->monitoring_model ?: 'gpt-4o-mini';

        // Fire and forget - we don't need to wait for the result, the tool-call is all that matters.
        $agent->prompt($prompt, model: $model);
    }
}
