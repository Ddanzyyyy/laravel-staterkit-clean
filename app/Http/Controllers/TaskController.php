<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(Request $request): Response
    {
        $view = $request->string('view', 'all')->toString();
        $listId = $request->integer('list', 0);
        $search = $request->string('q')->toString();

        $query = Task::query()
            ->where('user_id', $request->user()->id)
            ->where('is_completed', false)
            ->when($search, function ($query, string $search) {
                $query->where('title', 'like', "%{$search}%");
            });

        switch ($view) {
            case 'my-day':
                $query->whereDate('due_date', Carbon::today());
                break;
            case 'important':
                $query->where('is_important', true);
                break;
            case 'planned':
                $query->whereNotNull('due_date')->orderBy('due_date');
                break;
            default:
                $query->latest();
        }

        if ($listId > 0) {
            $query->where('task_list_id', $listId);
        }

        return Inertia::render('custom/tasks/Index', [
            'lists' => TaskList::where('user_id', $request->user()->id)->get(),
            'tasks' => $query->get(),
            'view' => $view,
            'listId' => $listId,
            'filters' => [
                'q' => $search,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_list_id' => ['nullable', 'integer'],
        ]);

        if (! empty($validated['task_list_id']) && ! $this->ownsList($request, (int) $validated['task_list_id'])) {
            abort(403);
        }

        $request->user()->tasks()->create($validated);

        return back();
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'is_completed' => ['sometimes', 'boolean'],
            'is_important' => ['sometimes', 'boolean'],
            'due_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'task_list_id' => ['nullable', 'integer'],
        ]);

        if (array_key_exists('task_list_id', $validated) && $validated['task_list_id'] !== null && ! $this->ownsList($request, (int) $validated['task_list_id'])) {
            abort(403);
        }

        $task->fill($validated)->save();

        return back();
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->delete();

        return back();
    }

    private function ownsList(Request $request, int $listId): bool
    {
        return TaskList::where('id', $listId)->where('user_id', $request->user()->id)->exists();
    }
}
