<?php

use App\Models\AgentConfig;
use App\Models\User;

beforeEach(function () {
    $this->student = User::factory()->student()->create();
    $this->teacher = User::factory()->teacher()->create();
    $this->agentConfig = AgentConfig::factory()->create(['allowed_groups' => [1]]);
});

test('students cannot access teacher pages', function (string $method, Closure $url) {
    $this->actingAs($this->student)
        ->call($method, $url($this))
        ->assertForbidden();
})->with([
    'agents index' => ['GET', fn ($test) => route('teacher.agents.index')],
    'agents create' => ['GET', fn ($test) => route('teacher.agents.create')],
    'agents store' => ['POST', fn ($test) => route('teacher.agents.store')],
    'agents edit' => ['GET', fn ($test) => route('teacher.agents.edit', $test->agentConfig)],
    'agents update' => ['PUT', fn ($test) => route('teacher.agents.update', $test->agentConfig)],
    'agents destroy' => ['DELETE', fn ($test) => route('teacher.agents.destroy', $test->agentConfig)],
    'revoke history' => ['POST', fn ($test) => route('teacher.agents.revokeHistory', $test->agentConfig)],
    'attachment download' => ['GET', fn ($test) => route('teacher.agents.attachments.download', [$test->agentConfig, 'x'])],
    'chats index' => ['GET', fn ($test) => route('teacher.chats.index')],
    'chats show' => ['GET', fn ($test) => route('teacher.chats.show', 'x')],
    'usage index' => ['GET', fn ($test) => route('teacher.usage.index')],
    'usage invoice' => ['GET', fn ($test) => route('teacher.usage.invoice', $test->student->id)],
    'observations index' => ['GET', fn ($test) => route('teacher.observations.index')],
    'observations show' => ['GET', fn ($test) => route('teacher.observations.show', 'x')],
]);

test('guests are redirected to login from teacher pages', function () {
    $this->get(route('teacher.chats.index'))->assertRedirect(route('login'));
});

test('student messages are escaped when a teacher views a chat', function () {
    $conversationId = createConversation($this->student, $this->agentConfig);
    createConversationMessage($conversationId, $this->student, 'user', '<script>alert("user")</script>');
    createConversationMessage($conversationId, $this->student, 'assistant', '<img src=x onerror=alert("assistant")>');

    $this->actingAs($this->teacher)
        ->get(route('teacher.chats.show', $conversationId))
        ->assertOk()
        ->assertDontSee('<script>alert("user")</script>', false)
        ->assertDontSee('<img src=x onerror=alert("assistant")>', false)
        ->assertSee('&lt;script&gt;', false);
});

test('chat search highlights do not break escaping', function () {
    $conversationId = createConversation($this->student, $this->agentConfig);
    createConversationMessage($conversationId, $this->student, 'user', 'Look: <b>bold</b> & more');

    $this->actingAs($this->teacher)
        ->get(route('teacher.chats.index', ['q' => '<b>']))
        ->assertOk()
        ->assertDontSee('<b>bold</b>', false)
        ->assertSee('<mark class="bg-yellow-200 dark:bg-yellow-600/40 px-0.5 rounded">&lt;b&gt;</mark>', false);
});
