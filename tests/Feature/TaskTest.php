<?php

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('tasks index returns smart views filtered by query', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Task::factory()->dueToday()->create(['user_id' => $user->id]);
    Task::factory()->important()->create(['user_id' => $user->id]);
    Task::factory()->create(['user_id' => $user->id]);

    $this->get('/tasks?view=important')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('custom/tasks/Index')
            ->has('tasks', 1)
            ->where('view', 'important'));
});

test('tasks index filters by list', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $list = TaskList::factory()->create(['user_id' => $user->id]);
    Task::factory()->create(['user_id' => $user->id, 'task_list_id' => $list->id]);
    Task::factory()->create(['user_id' => $user->id]);

    $this->get('/tasks?list='.$list->id)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('tasks', 1)
            ->where('listId', $list->id));
});

test('tasks index shows completed tasks last', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Task::factory()->completed()->create(['user_id' => $user->id, 'title' => 'Done task']);
    Task::factory()->create(['user_id' => $user->id, 'title' => 'Active task']);

    $this->get('/tasks')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('tasks', 2)
            ->where('tasks.0.title', 'Active task')
            ->where('tasks.1.title', 'Done task'));
});

test('tasks index searches by title', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Task::factory()->create(['user_id' => $user->id, 'title' => 'Buy milk']);
    Task::factory()->create(['user_id' => $user->id, 'title' => 'Pay rent']);

    $this->get('/tasks?q=milk')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('tasks', 1)
            ->where('tasks.0.title', 'Buy milk')
            ->where('filters.q', 'milk'));
});

test('user can create a task', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post('/tasks', ['title' => 'New task'])
        ->assertRedirect();

    $this->assertDatabaseHas('tasks', ['user_id' => $user->id, 'title' => 'New task']);
});

test('user can toggle and update a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $this->patch('/tasks/'.$task->id, ['is_completed' => true])
        ->assertRedirect();

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_completed' => true]);
});

test('user can update task color', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $this->patch('/tasks/'.$task->id, ['color' => '#ff0000'])
        ->assertRedirect();

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'color' => '#ff0000']);
});

test('user cannot update or delete another user task', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $other->id]);
    $this->actingAs($user);

    $this->patch('/tasks/'.$task->id, ['title' => 'Hacked'])->assertForbidden();
    $this->delete('/tasks/'.$task->id)->assertForbidden();
});

test('user can delete a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);

    $this->delete('/tasks/'.$task->id)->assertRedirect();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
