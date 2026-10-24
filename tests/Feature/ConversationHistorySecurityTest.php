<?php

use App\Models\AgentConfig;
use App\Models\User;

beforeEach(function () {
    fakeUserGroups([1]);

    $this->student = User::factory()->student()->create();
    $this->otherStudent = User::factory()->student()->create();
    $this->agentConfig = AgentConfig::factory()->create(['allowed_groups' => [1]]);
});

test('a student only sees their own conversations', function () {
    $ownConversationId = createConversation($this->student, $this->agentConfig);
    createConversation($this->otherStudent, $this->agentConfig);

    $this->actingAs($this->student)
        ->getJson('/api/conversations?agentConfigId='.$this->agentConfig->id)
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.id', $ownConversationId);
});

test('a student cannot list conversations of an agent outside their groups', function () {
    $agentConfig = AgentConfig::factory()->create(['allowed_groups' => [2]]);
    createConversation($this->student, $agentConfig);

    $this->actingAs($this->student)
        ->getJson('/api/conversations?agentConfigId='.$agentConfig->id)
        ->assertForbidden();
});

test('a student cannot list conversations when history is disabled', function () {
    $this->agentConfig->update(['history_is_disabled' => true]);
    createConversation($this->student, $this->agentConfig);

    $this->actingAs($this->student)
        ->getJson('/api/conversations?agentConfigId='.$this->agentConfig->id)
        ->assertForbidden();
});

test('a student cannot read messages of another student', function () {
    $conversationId = createConversation($this->otherStudent, $this->agentConfig);
    createConversationMessage($conversationId, $this->otherStudent, 'user', 'My secret essay');

    $this->actingAs($this->student)
        ->getJson("/api/conversations/{$conversationId}/messages")
        ->assertNotFound()
        ->assertDontSee('My secret essay');
});

test('a student cannot read messages of a revoked conversation', function () {
    $conversationId = createConversation($this->student, $this->agentConfig, ['revoked_at' => now()]);
    createConversationMessage($conversationId, $this->student, 'user', 'Old question');

    $this->actingAs($this->student)
        ->getJson("/api/conversations/{$conversationId}/messages")
        ->assertNotFound();
});

test('a student cannot read messages when history is disabled', function () {
    $this->agentConfig->update(['history_is_disabled' => true]);
    $conversationId = createConversation($this->student, $this->agentConfig);
    createConversationMessage($conversationId, $this->student, 'user', 'Old question');

    $this->actingAs($this->student)
        ->getJson("/api/conversations/{$conversationId}/messages")
        ->assertForbidden();
});

test('a student cannot read messages after losing access to the agent', function () {
    $this->agentConfig->update(['allowed_groups' => [2]]);
    $conversationId = createConversation($this->student, $this->agentConfig);
    createConversationMessage($conversationId, $this->student, 'user', 'Old question');

    $this->actingAs($this->student)
        ->getJson("/api/conversations/{$conversationId}/messages")
        ->assertForbidden();
});

test('a student can read messages of their own conversation', function () {
    $conversationId = createConversation($this->student, $this->agentConfig);
    createConversationMessage($conversationId, $this->student, 'user', 'My question');

    $this->actingAs($this->student)
        ->getJson("/api/conversations/{$conversationId}/messages")
        ->assertOk()
        ->assertJsonPath('0.content', 'My question');
});

test('a student cannot open the chat page of an agent outside their groups', function () {
    $agentConfig = AgentConfig::factory()->create(['allowed_groups' => [2]]);

    $this->actingAs($this->student)
        ->get(route('chat.show', $agentConfig))
        ->assertForbidden();
});
