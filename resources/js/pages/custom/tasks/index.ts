import type { BreadcrumbItem, Task, TaskList } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

export interface Props {
    lists: TaskList[];
    tasks: Task[];
    counts: Record<string, number>;
    view: string;
    listId: number;
    filters: { q?: string };
}

export function useTasksPage(props: Props) {
    const breadcrumbs: BreadcrumbItem[] = [{ title: 'Tasks', href: '/tasks' }];

    const viewTitle = computed(() => {
        if (props.listId > 0) {
            return props.lists.find((l) => l.id === props.listId)?.name ?? 'Tasks';
        }
        return { 'my-day': 'My Day', important: 'Important', planned: 'Planned', all: 'Tasks' }[props.view] ?? 'Tasks';
    });

    const currentList = computed(() => props.lists.find((l) => l.id === props.listId) ?? null);

    const renameTarget = ref<TaskList | null>(null);
    const renameForm = useForm({ name: '' });

    const openRename = () => {
        if (!currentList.value) {
            return;
        }
        renameForm.clearErrors();
        renameForm.name = currentList.value.name;
        renameTarget.value = currentList.value;
    };

    const rename = () => {
        if (!renameTarget.value) {
            return;
        }
        const list = renameTarget.value;
        renameForm.patch(route('task-lists.update', { id: String(list.id) }), {
            onSuccess: () => {
                renameTarget.value = null;
                toast.success('List renamed');
            },
        });
    };

    const deleteTarget = ref<TaskList | null>(null);

    const openDelete = () => {
        deleteTarget.value = currentList.value;
    };

    const destroyList = () => {
        if (!deleteTarget.value) {
            return;
        }
        const list = deleteTarget.value;
        deleteTarget.value = null;
        router.delete(route('task-lists.destroy', { id: String(list.id) }), {
            onSuccess: () => toast.success('List deleted'),
        });
    };

    const newTask = useForm({ title: '', task_list_id: props.listId > 0 ? String(props.listId) : '' });

    const addTask = () => {
        newTask.post(route('tasks.store'), {
            onSuccess: () => {
                newTask.reset();
                toast.success('Task added');
            },
        });
    };

    const activeTask = ref<Task | null>(null);
    const detailForm = useForm<{ title: string; due_date: string; note: string; is_important: boolean; task_list_id: string; color: string }>({
        title: '',
        due_date: '',
        note: '',
        is_important: false,
        task_list_id: '',
        color: '',
    });

    const handleColorInput = (event: Event) => {
        detailForm.color = (event.target as HTMLInputElement).value;
    };

    const openDetail = (task: Task) => {
        detailForm.clearErrors();
        detailForm.defaults({
            title: task.title,
            due_date: task.due_date ?? '',
            note: task.note ?? '',
            is_important: task.is_important,
            task_list_id: task.task_list_id ? String(task.task_list_id) : '',
            color: task.color ?? '',
        });
        detailForm.reset();
        activeTask.value = task;
    };

    const saveDetail = () => {
        if (!activeTask.value) {
            return;
        }
        detailForm.patch(route('tasks.update', { id: String(activeTask.value.id) }), {
            onSuccess: () => {
                activeTask.value = null;
                toast.success('Task updated');
            },
        });
    };

    const toggleComplete = (task: Task) => {
        router.patch(route('tasks.update', { id: String(task.id) }), { is_completed: !task.is_completed });
    };

    const toggleImportant = (task: Task) => {
        router.patch(route('tasks.update', { id: String(task.id) }), { is_important: !task.is_important });
    };

    const taskToDelete = ref<Task | null>(null);

    const destroyTask = () => {
        if (!taskToDelete.value) {
            return;
        }
        const task = taskToDelete.value;
        taskToDelete.value = null;
        router.delete(route('tasks.destroy', { id: String(task.id) }), {
            onSuccess: () => toast.success('Task deleted'),
        });
    };

    return {
        breadcrumbs,
        viewTitle,
        currentList,
        renameTarget,
        renameForm,
        openRename,
        rename,
        deleteTarget,
        openDelete,
        destroyList,
        newTask,
        addTask,
        activeTask,
        detailForm,
        handleColorInput,
        openDetail,
        saveDetail,
        toggleComplete,
        toggleImportant,
        taskToDelete,
        destroyTask,
    };
}
