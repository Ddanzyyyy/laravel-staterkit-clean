<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import type { TaskList } from '@/types';
import { Link, router, useForm } from '@inertiajs/vue3';
import { CalendarDays, CheckCircle2, Ellipsis, Flag, Inbox, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    lists: TaskList[];
    view: string;
    listId: number;
    counts: Record<string, number>;
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

const renameTarget = ref<TaskList | null>(null);
const renameForm = useForm({ name: '' });

const openRename = (list: TaskList) => {
    renameForm.clearErrors();
    renameForm.reset({ name: list.name });
    renameTarget.value = list;
};

const rename = () => {
    if (!renameTarget.value) {
        return;
    }
    const list = renameTarget.value;
    renameForm.patch(route('task-lists.update', String(list.id)), {
        onSuccess: () => {
            renameTarget.value = null;
            toast.success('List renamed');
        },
    });
};

const deleteTarget = ref<TaskList | null>(null);

const openDelete = (list: TaskList) => {
    deleteTarget.value = list;
};

const destroy = () => {
    if (!deleteTarget.value) {
        return;
    }
    const list = deleteTarget.value;
    deleteTarget.value = null;
    router.delete(route('task-lists.destroy', String(list.id)), {
        onSuccess: () => toast.success('List deleted'),
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
            <component :is="view.icon" class="size-4" />
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
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="size-6 opacity-0 group-hover:opacity-100">
                        <Ellipsis class="size-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="start">
                    <DropdownMenuItem @click="openRename(list)">
                        <Pencil class="size-4" />
                        Rename
                    </DropdownMenuItem>
                    <DropdownMenuItem variant="destructive" @click="openDelete(list)">
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

    <Dialog :open="renameTarget !== null" @update:open="(open: boolean) => !open && (renameTarget = null)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Rename list</DialogTitle>
            </DialogHeader>
            <form id="rename-list" class="flex flex-col gap-2" @submit.prevent="rename">
                <Input v-model="renameForm.name" autofocus />
            </form>
            <DialogFooter>
                <Button variant="outline" @click="renameTarget = null">Cancel</Button>
                <Button type="submit" form="rename-list" :disabled="renameForm.processing">Save</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <Dialog :open="deleteTarget !== null" @update:open="(open: boolean) => !open && (deleteTarget = null)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Delete list</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete "{{ deleteTarget?.name }}"? Tasks in it will be kept.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="deleteTarget = null">Cancel</Button>
                <Button variant="destructive" @click="destroy">Delete</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
