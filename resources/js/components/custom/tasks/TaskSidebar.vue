<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { TaskList } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { CalendarDays, CheckCircle2, Flag, Inbox, Plus } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    lists: TaskList[];
    view: string;
    listId: number;
    counts: Record<string, number>;
}>();

const smartViews = [
    { key: 'my-day', label: 'My Day', icon: CalendarDays, color: 'text-amber-500' },
    { key: 'important', label: 'Important', icon: Flag, color: 'text-red-500' },
    { key: 'planned', label: 'Planned', icon: CalendarDays, color: 'text-blue-500' },
    { key: 'all', label: 'Tasks', icon: Inbox, color: 'text-muted-foreground' },
];

const newList = useForm({ name: '' });

const createList = () => {
    newList.post(route('task-lists.store'), {
        onSuccess: () => {
            newList.reset();
            toast.success('List created');
        },
    });
};

const active = (key: string, id?: number) => (id !== undefined ? props.listId === id : props.view === key);
</script>

<template>
    <div class="flex h-full w-64 flex-col gap-1 overflow-y-auto border-r p-3">
        <Link
            v-for="view in smartViews"
            :key="view.key"
            :href="route('tasks.index', { view: view.key })"
            class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-accent"
            :class="{ 'bg-accent text-accent-foreground': active(view.key) }"
        >
            <component :is="view.icon" class="size-4" :class="view.color" />
            <span class="flex-1">{{ view.label }}</span>
            <span class="text-xs tabular-nums text-muted-foreground">{{ counts[view.key] }}</span>
        </Link>

        <div class="mt-2 flex items-center justify-between px-2 text-xs font-semibold uppercase text-muted-foreground">
            <span>My Lists</span>
        </div>

        <Link
            v-for="list in lists"
            :key="list.id"
            :href="route('tasks.index', { list: list.id })"
            class="group flex items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-accent"
            :class="{ 'bg-accent text-accent-foreground': active('', list.id) }"
        >
            <span class="size-2.5 rounded-full" :style="{ backgroundColor: list.color }" />
            <span class="flex-1 truncate">{{ list.name }}</span>
            <span class="text-xs tabular-nums text-muted-foreground">{{ list.tasks_count }}</span>
        </Link>

        <div class="mt-auto flex flex-col gap-2 px-2 pt-4">
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <CheckCircle2 class="size-4" />
                All tasks are kept forever
            </div>
            <form class="flex items-center gap-2" @submit.prevent="createList">
                <Input v-model="newList.name" placeholder="New list..." class="h-8 text-sm" />
                <Button type="submit" size="icon" class="size-8 shrink-0" variant="ghost" :disabled="newList.processing">
                    <Plus class="size-4" />
                </Button>
            </form>
        </div>
    </div>
</template>
