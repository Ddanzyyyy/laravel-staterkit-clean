<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import type { Task } from '@/types';
import { Star } from 'lucide-vue-next';

defineProps<{
    task: Task;
    showCompleted?: boolean;
}>();

const emit = defineEmits<{
    toggleComplete: [task: Task];
    toggleImportant: [task: Task];
    open: [task: Task];
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
        <span v-if="task.color" class="size-2.5 shrink-0 rounded-full" :style="{ backgroundColor: task.color }" />
        <Checkbox
            :checked="task.is_completed"
            class="size-5 cursor-pointer"
            @update:checked="emit('toggleComplete', task)"
        />
        <button class="flex-1 truncate text-left text-sm" @click="emit('open', task)">
            <span :class="{ 'line-through': task.is_completed }">{{ task.title }}</span>
            <span v-if="dueLabel(task.due_date)" class="ml-2 text-xs text-muted-foreground">
                {{ dueLabel(task.due_date) }}
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
    </div>
</template>
