<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Database\Eloquent\Builder;
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
            ->orderBy('is_completed', 'asc')
            ->when($search, function ($query, string $search) {
                $query->where('title', 'like', "%{$search}%");
            });

        /** @var \Closure(): Builder $userTasks */
        $userTasks = fn () => Task::query()->where('user_id', $request->user()->id)->where('is_completed', false);

        $counts = [
            'my-day' => $userTasks()->whereDate('due_date', Carbon::today())->count(),
            'important' => $userTasks()->where('is_important', true)->count(),
            'planned' => $userTasks()->whereNotNull('due_date')->count(),
            'all' => $userTasks()->count(),
        ];

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
            'lists' => TaskList::withCount(['tasks as tasks_count' => fn ($query) => $query->where('is_completed', false)])->where('user_id', $request->user()->id)->get(),
            'tasks' => $query->get(),
            'counts' => $counts,
            'view' => $view,
            'listId' => $listId,
            'filters' => [
                'q' => $search,
            ],
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $request->user()->tasks()->create($request->validated());

        return back();
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->fill($request->validated())->save();

        return back();
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        abort_unless($task->user_id === $request->user()->id, 403);

        $task->delete();

        return back();
    }
}
