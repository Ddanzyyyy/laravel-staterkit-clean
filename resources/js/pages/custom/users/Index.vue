<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { watch } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Users',
        href: '/users',
    },
];

interface UserRow {
    id: number;
    name: string;
    email: string;
}

interface Paginator {
    data: UserRow[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    users: Paginator;
    flash?: { success?: string | null };
}>();

watch(
    () => props.flash?.success,
    (message) => {
        if (message) {
            toast.success(message);
        }
    },
    { immediate: true },
);

const destroy = (user: UserRow) => {
    if (confirm(`Delete ${user.name}?`)) {
        router.delete(`/users/${user.id}`, {
            onSuccess: () => toast.success(`User "${user.name}" deleted`),
            onError: () => toast.error('Failed to delete user'),
        });
    }
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0">
                    <CardTitle>Users</CardTitle>
                    <Button as-child>
                        <Link :href="route('users.create')">
                            <Plus class="h-4 w-4" />
                            Add User
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-muted-foreground">
                                    <th class="py-2 pr-4">Name</th>
                                    <th class="py-2 pr-4">Email</th>
                                    <th class="py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="border-b last:border-0">
                                    <td class="py-3 pr-4 font-medium">{{ user.name }}</td>
                                    <td class="py-3 pr-4 text-muted-foreground">{{ user.email }}</td>
                                    <td class="py-3">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="outline" size="sm" as-child>
                                                <Link :href="`/users/${user.id}/edit`">
                                                    <Pencil class="h-3.5 w-3.5" />
                                                    Edit
                                                </Link>
                                            </Button>
                                            <Button variant="destructive" size="sm" @click="destroy(user)">
                                                <Trash2 class="h-3.5 w-3.5" />
                                                Delete
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="3" class="py-6 text-center text-muted-foreground">No users yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="users.links.length > 3" class="mt-4 flex justify-center gap-1">
                        <template v-for="link in users.links" :key="link.label">
                            <Button v-if="link.url" :variant="link.active ? 'default' : 'outline'" size="sm" as-child>
                                <Link :href="link.url">{{ link.label }}</Link>
                            </Button>
                            <Button v-else variant="outline" size="sm" disabled>
                                {{ link.label }}
                            </Button>
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
