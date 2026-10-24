<?php

use App\Ai\Agents\CustomAgent;
use App\Models\AgentConfig;
use App\Models\User;
use App\Services\SdApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Fake the sdlogin API so the signed in user belongs to the given groups.
 *
 * @param  array<int, int>  $groupIds
 */
function fakeUserGroups(array $groupIds): void
{
    $sdApiService = Mockery::mock(SdApiService::class)->makePartial();
    $sdApiService->shouldReceive('getPersonalInfo')
        ->andReturn(['groups' => array_map(fn (int $id) => ['id' => $id], $groupIds)]);

    app()->instance(SdApiService::class, $sdApiService);
}

/**
 * Insert a conversation directly, mirroring how the AI SDK stores them.
 *
 * @param  array<string, mixed>  $attributes
 */
function createConversation(User $user, ?AgentConfig $agentConfig, array $attributes = []): string
{
    $conversationId = (string) Str::uuid7();

    DB::table('agent_conversations')->insert([
        'id' => $conversationId,
        'user_id' => $user->id,
        'agent_config_id' => $agentConfig?->id,
        'title' => 'Conversation of '.$user->name,
        'created_at' => now(),
        'updated_at' => now(),
        ...$attributes,
    ]);

    return $conversationId;
}

/**
 * Insert a message into a conversation. Pass an explicit, sortable id when message order matters.
 */
function createConversationMessage(string $conversationId, User $user, string $role, string $content, ?string $id = null): void
{
    DB::table('agent_conversation_messages')->insert([
        'id' => $id ?? (string) Str::uuid7(),
        'conversation_id' => $conversationId,
        'user_id' => $user->id,
        'agent' => CustomAgent::class,
        'role' => $role,
        'content' => $content,
        'attachments' => '[]',
        'tool_calls' => '[]',
        'tool_results' => '[]',
        'usage' => '[]',
        'meta' => '[]',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
