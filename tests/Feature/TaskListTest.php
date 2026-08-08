<?php

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;

test('user can create a list', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post('/task-lists', ['name' => 'Work'])
        ->assertRedirect();

    $this->assertDatabaseHas('task_lists', ['user_id' => $user->id, 'name' => 'Work']);
});

test('user can rename a list', function () {
    $user = User::factory()->create();
    $list = TaskList::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $this->patch('/task-lists/'.$list->id, ['name' => 'Personal'])
        ->assertRedirect();

    $this->assertDatabaseHas('task_lists', ['id' => $list->id, 'name' => 'Personal']);
});

test('user can delete a list without deleting its tasks', function () {
    $user = User::factory()->create();
    $list = TaskList::factory()->create(['user_id' => $user->id]);
    $task = Task::factory()->create(['user_id' => $user->id, 'task_list_id' => $list->id]);
    $this->actingAs($user);

    $this->delete('/task-lists/'.$list->id)->assertRedirect();

    $this->assertDatabaseMissing('task_lists', ['id' => $list->id]);
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});

test('user cannot rename or delete another user list', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $list = TaskList::factory()->create(['user_id' => $other->id]);
    $this->actingAs($user);

    $this->patch('/task-lists/'.$list->id, ['name' => 'Hacked'])->assertForbidden();
    $this->delete('/task-lists/'.$list->id)->assertForbidden();
});
