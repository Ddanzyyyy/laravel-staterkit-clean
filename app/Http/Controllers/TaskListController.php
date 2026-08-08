<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['sometimes', 'string', 'max:20'],
        ]);

        $request->user()->taskLists()->create($validated);

        return back();
    }

    public function update(Request $request, TaskList $list): RedirectResponse
    {
        abort_unless($list->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'color' => ['sometimes', 'string', 'max:20'],
        ]);

        $list->fill($validated)->save();

        return back();
    }

    public function destroy(Request $request, TaskList $list): RedirectResponse
    {
        abort_unless($list->user_id === $request->user()->id, 403);

        $list->delete();

        return back();
    }
}
