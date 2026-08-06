<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

defineProps<{
    user: User;
}>();

const { getInitials } = useInitials();
</script>

<template>
    <div class="flex items-center gap-3">
        <Avatar class="h-12 w-12 overflow-hidden rounded-full">
            <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
            <AvatarFallback class="rounded-full bg-primary text-lg text-primary-foreground">
                {{ getInitials(user.name) }}
            </AvatarFallback>
        </Avatar>
        <div>
            <p class="font-semibold">{{ user.name }}</p>
            <p class="text-sm text-muted-foreground">{{ user.email }}</p>
        </div>
    </div>
</template>
