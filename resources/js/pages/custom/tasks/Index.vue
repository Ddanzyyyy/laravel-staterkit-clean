<script setup lang="ts">
import TaskItem from '@/components/custom/tasks/TaskItem.vue';
import TaskSidebar from '@/components/custom/tasks/TaskSidebar.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Task, TaskList } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

interface Props {
    lists: TaskList[];
    tasks: Task[];
    view: string;
    listId: number;
    filters: { q?: string };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Tasks', href: '/tasks' },
];

const viewTitle = computed(() => {
    if (props.listId > 0) {
        return props.lists.find((l) => l.id === props.listId)?.name ?? 'Tasks';
    }
    return { 'my-day': 'My Day', important: 'Important', planned: 'Planned', all: 'Tasks' }[props.view] ?? 'Tasks';
});

const newTask = useForm({ title: '', task_list_id: props.listId > 0 ? String(props.listId) : '' });

const addTask = () => {
    newTask.post(route('tasks.store'), {
        onSuccess: () => {
            newTask.reset({ title: '', task_list_id: props.listId > 0 ? String(props.listId) : '' });
            toast.success('Task added');
        },
    });
};

const activeTask = ref<Task | null>(null);
const detailForm = useForm({ title: '', due_date: '', note: '', is_important: false, task_list_id: '', color: '' });

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
    detailForm.patch(route('tasks.update', String(activeTask.value.id)), {
        onSuccess: () => {
            activeTask.value = null;
            toast.success('Task updated');
        },
    });
};

const toggleComplete = (task: Task) => {
    router.patch(route('tasks.update', String(task.id)), { is_completed: !task.is_completed });
};

const toggleImportant = (task: Task) => {
    router.patch(route('tasks.update', String(task.id)), { is_important: !task.is_important });
};

const destroyTask = (task: Task) => {
    if (window.confirm(`Delete "${task.title}"?`)) {
        router.delete(route('tasks.destroy', String(task.id)), {
            onSuccess: () => toast.success('Task deleted'),
        });
    }
};
</script>

<template>
    <Head title="Tasks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full min-h-0 flex-1">
            <TaskSidebar :lists="lists" :view="view" :list-id="listId" />

            <div class="flex flex-1 flex-col overflow-y-auto p-4">
                <h1 class="mb-4 text-2xl font-bold">{{ viewTitle }}</h1>

                <div class="flex flex-col gap-1">
                    <TaskItem
                        v-for="task in tasks"
                        :key="task.id"
                        :task="task"
                        @toggle-complete="toggleComplete"
                        @toggle-important="toggleImportant"
                        @open="openDetail"
                    />
                    <p v-if="tasks.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                        No tasks here. Add one below.
                    </p>
                </div>

                <form class="mt-4 flex items-center gap-2" @submit.prevent="addTask">
                    <Plus class="size-4 shrink-0 text-muted-foreground" />
                    <Input v-model="newTask.title" placeholder="Add a task..." class="flex-1" />
                    <Button type="submit" :disabled="newTask.processing">Add</Button>
                </form>
            </div>
        </div>
    </AppLayout>

    <Dialog :open="activeTask !== null" @update:open="(open: boolean) => !open && (activeTask = null)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Edit task</DialogTitle>
            </DialogHeader>
            <form id="task-detail" class="flex flex-col gap-4" @submit.prevent="saveDetail">
                <div class="flex flex-col gap-2">
                    <Label>Title</Label>
                    <Input v-model="detailForm.title" />
                </div>
                <div class="flex flex-col gap-2">
                    <Label>Due date</Label>
                    <Input v-model="detailForm.due_date" type="date" />
                </div>
                <div class="flex flex-col gap-2">
                    <Label>Note</Label>
                    <textarea v-model="detailForm.note" rows="3" class="rounded-md border bg-background px-2 py-1.5 text-sm" />
                </div>
                <div class="flex items-center gap-2">
                    <Checkbox v-model:checked="detailForm.is_important" id="detail-important" />
                    <Label for="detail-important">Important</Label>
                </div>
                <div class="flex items-center gap-2">
                    <Label>Color</Label>
                    <input type="color" :value="detailForm.color || '#6366f1'" class="h-8 w-12 cursor-pointer rounded border bg-background p-0.5" @input="detailForm.color = ($event.target as HTMLInputElement).value" />
                    <Button v-if="detailForm.color" type="button" variant="ghost" size="sm" @click="detailForm.color = ''">Reset</Button>
                </div>
                <div class="flex flex-col gap-2">
                    <Label>List</Label>
                    <select v-model="detailForm.task_list_id" class="rounded-md border bg-background px-2 py-1.5 text-sm">
                        <option value="">No list</option>
                        <option v-for="list in lists" :key="list.id" :value="String(list.id)">{{ list.name }}</option>
                    </select>
                </div>
            </form>
            <DialogFooter class="flex items-center justify-between">
                <Button type="button" variant="destructive" @click="activeTask && destroyTask(activeTask)">
                    Delete
                </Button>
                <Button type="submit" form="task-detail" :disabled="detailForm.processing">Save</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
