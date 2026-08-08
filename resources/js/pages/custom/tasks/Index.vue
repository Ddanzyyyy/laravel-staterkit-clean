<script setup lang="ts">
import TaskItem from '@/components/custom/tasks/TaskItem.vue';
import TaskSidebar from '@/components/custom/tasks/TaskSidebar.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Ellipsis, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { useTasksPage, type Props } from './index';

const props = defineProps<Props>();

const {
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
} = useTasksPage(props);
</script>

<template>
    <Head title="Tasks" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full min-h-0 flex-1">
            <TaskSidebar :lists="lists" :view="view" :list-id="listId" :counts="counts" />

            <div class="flex min-h-0 flex-1 flex-col p-4">
                <div class="min-h-0 flex-1 overflow-y-auto">
                    <div class="mb-4 flex items-center justify-between">
                        <h1 class="text-2xl font-bold">{{ viewTitle }}</h1>
                        <DropdownMenu v-if="currentList">
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="size-8">
                                    <Ellipsis class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem @click="openRename">
                                    <Pencil class="size-4" />
                                    Rename
                                </DropdownMenuItem>
                                <DropdownMenuItem variant="destructive" @click="openDelete">
                                    <Trash2 class="size-4" />
                                    Delete
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>

                    <div class="flex flex-col gap-1">
                        <TaskItem
                            v-for="task in tasks"
                            :key="task.id"
                            :task="task"
                            @toggle-complete="toggleComplete"
                            @toggle-important="toggleImportant"
                            @open="openDetail"
                            @delete="taskToDelete = $event"
                        />
                        <p v-if="tasks.length === 0" class="py-8 text-center text-sm text-muted-foreground">No tasks here. Add one below.</p>
                    </div>
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
                    <input
                        type="color"
                        :value="detailForm.color || '#6366f1'"
                        class="h-8 w-12 cursor-pointer rounded border bg-background p-0.5"
                        @input="handleColorInput"
                    />
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
                <Button type="submit" form="task-detail" :disabled="detailForm.processing">Save</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <Dialog :open="taskToDelete !== null" @update:open="(open: boolean) => !open && (taskToDelete = null)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Delete task</DialogTitle>
                <DialogDescription> Are you sure you want to delete "{{ taskToDelete?.title }}"? This action cannot be undone. </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="taskToDelete = null">Cancel</Button>
                <Button variant="destructive" @click="destroyTask">Delete</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

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
                <DialogDescription> Are you sure you want to delete "{{ deleteTarget?.name }}"? Tasks in it will be kept. </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="deleteTarget = null">Cancel</Button>
                <Button variant="destructive" @click="destroyList">Delete</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
