<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import type { TaskList } from '@/types';
import { Link, router, useForm } from '@inertiajs/vue3';
import { CalendarDays, CheckCircle2, Ellipsis, Flag, Inbox, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps<{
    lists: TaskList[];
    view: string;
    listId: number;
}>();

const smartViews = [
    { key: 'my-day', label: 'My Day', icon: CalendarDays },
    { key: 'important', label: 'Important', icon: Flag },
    { key: 'planned', label: 'Planned', icon: CalendarDays },
    { key: 'all', label: 'Tasks', icon: Inbox },
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

const rename = (list: TaskList) => {
    const name = window.prompt('Rename list', list.name);
    if (name && name.trim() && name !== list.name) {
        router.patch(route('task-lists.update', list.id), { name: name.trim() }, {
            onSuccess: () => toast.success('List renamed'),
        });
    }
};

const destroy = (list: TaskList) => {
    if (window.confirm(`Delete "${list.name}"? Tasks in it will be kept.`)) {
        router.delete(route('task-lists.destroy', list.id), {
            onSuccess: () => toast.success('List deleted'),
        });
    }
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
            <component :is="view.icon" class="size-4" />
            {{ view.label }}
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
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="size-6 opacity-0 group-hover:opacity-100">
                        <Ellipsis class="size-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start">
                    <DropdownMenuItem @click="rename(list)">
                        <Pencil class="size-4" />
                        Rename
                    </DropdownMenuItem>
                    <DropdownMenuItem variant="destructive" @click="destroy(list)">
                        <Trash2 class="size-4" />
                        Delete
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </Link>

        <form class="mt-2 flex items-center gap-2 px-2" @submit.prevent="createList">
            <Input v-model="newList.name" placeholder="New list..." class="h-8 text-sm" />
            <Button type="submit" size="icon" class="size-8 shrink-0" variant="ghost" :disabled="newList.processing">
                <Plus class="size-4" />
            </Button>
        </form>

        <div class="mt-auto flex items-center gap-2 px-2 pt-4 text-xs text-muted-foreground">
            <CheckCircle2 class="size-4" />
            All tasks are kept forever
        </div>
    </div>
</template>
