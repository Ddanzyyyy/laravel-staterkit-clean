<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import type { Task } from '@/types';
import { CalendarClock, Star, Trash2 } from 'lucide-vue-next';

defineProps<{
    task: Task;
    showCompleted?: boolean;
}>();

const emit = defineEmits<{
    toggleComplete: [task: Task];
    toggleImportant: [task: Task];
    open: [task: Task];
    delete: [task: Task];
}>();

const dueLabel = (due: string | null): string | null => {
    if (!due) {
        return null;
    }
    const today = new Date().toISOString().slice(0, 10);
    return due === today ? 'Today' : new Date(due + 'T00:00:00').toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
};
</script>

<template>
    <div
        class="group flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-accent/50"
        :class="{ 'opacity-50': task.is_completed }"
    >
        <Checkbox
            :checked="task.is_completed"
            class="size-5 cursor-pointer"
            @update:checked="emit('toggleComplete', task)"
        />
        <button class="min-w-0 flex-1 text-left text-sm" @click="emit('open', task)">
            <span class="block truncate" :class="{ 'line-through': task.is_completed }">{{ task.title }}</span>
            <span v-if="task.note || dueLabel(task.due_date)" class="mt-0.5 flex items-center gap-1.5 text-xs text-muted-foreground">
                <span v-if="task.note" class="min-w-0 truncate">{{ task.note }}</span>
                <span v-if="task.note && dueLabel(task.due_date)" class="shrink-0 font-bold">•</span>
                <span v-if="dueLabel(task.due_date)" class="flex shrink-0 items-center gap-1">
                    <CalendarClock class="size-3" />
                    {{ dueLabel(task.due_date) }}
                </span>
            </span>
        </button>
        <button
            class="text-muted-foreground hover:text-foreground"
            :title="task.is_important ? 'Remove from important' : 'Mark as important'"
            @click="emit('toggleImportant', task)"
        >
            <Star
                class="size-4"
                :class="task.is_important ? 'fill-yellow-400 text-yellow-400' : 'opacity-0 group-hover:opacity-100'"
            />
        </button>
        <button
            class="text-muted-foreground opacity-0 hover:text-destructive group-hover:opacity-100"
            title="Delete task"
            @click="emit('delete', task)"
        >
            <Trash2 class="size-4" />
        </button>
    </div>
</template>
