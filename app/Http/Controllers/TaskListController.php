<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskListRequest;
use App\Http\Requests\UpdateTaskListRequest;
use App\Models\TaskList;
use Illuminate\Http\RedirectResponse;

class TaskListController extends Controller
{
    public function store(StoreTaskListRequest $request): RedirectResponse
    {
        $request->user()->taskLists()->create($request->validated());

        return back();
    }

    public function update(UpdateTaskListRequest $request, TaskList $list): RedirectResponse
    {
        abort_unless($list->user_id === $request->user()->id, 403);

        $list->fill($request->validated())->save();

        return back();
    }

    public function destroy(TaskList $list): RedirectResponse
    {
        abort_unless($list->user_id === auth()->id(), 403);

        $list->delete();

        return back();
    }
}
