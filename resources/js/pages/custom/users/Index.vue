<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

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
    created_at: string;
}

interface Paginator {
    data: UserRow[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    users: Paginator;
    filters: { search?: string; per_page?: number };
}>();

const userToDelete = ref<UserRow | null>(null);

const search = ref(props.filters.search ?? '');
const perPage = ref(props.filters.per_page ?? 10);

let debounceTimer: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/users', { search: value || undefined, per_page: perPage.value }, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
});

watch(perPage, (value) => {
    router.get('/users', { search: search.value || undefined, per_page: value }, { preserveState: true, preserveScroll: true });
});

const destroy = () => {
    if (!userToDelete.value) {
        return;
    }

    const user = userToDelete.value;
    userToDelete.value = null;

    router.delete(`/users/${user.id}`, {
        onSuccess: () => toast.success(`User "${user.name}" deleted`),
        onError: () => toast.error('Failed to delete user'),
    });
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0">
                    <CardTitle>Users</CardTitle>
                    <div class="flex items-center gap-2">
                        <Input v-model="search" type="search" placeholder="Search name or email..." class="max-w-xs" />
                        <Button as-child>
                            <Link :href="route('users.create')">
                                <Plus class="h-4 w-4" />
                                Add User
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-muted-foreground">
                                    <th class="py-2 pr-4">Name</th>
                                    <th class="py-2 pr-4">Email</th>
                                    <th class="py-2 pr-4">Created</th>
                                    <th class="py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="border-b last:border-0">
                                    <td class="py-3 pr-4 font-medium">{{ user.name }}</td>
                                    <td class="py-3 pr-4 text-muted-foreground">{{ user.email }}</td>
                                    <td class="py-3 pr-4 text-muted-foreground">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                    <td class="py-3">
                                        <div class="flex justify-end gap-2">
                                            <Button variant="outline" size="sm" as-child>
                                                <Link :href="`/users/${user.id}/edit`">
                                                    <Pencil class="h-3.5 w-3.5" />
                                                    Edit
                                                </Link>
                                            </Button>
                                            <Button variant="destructive" size="sm" @click="userToDelete = user">
                                                <Trash2 class="h-3.5 w-3.5" />
                                                Delete
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="4" class="py-6 text-center text-muted-foreground">No users yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <span>Rows per page</span>
                            <select v-model.number="perPage" class="rounded-md border bg-background px-2 py-1 text-sm">
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                            </select>
                        </div>
                        <div v-if="users.links.length > 3" class="flex justify-center gap-1">
                            <template v-for="link in users.links" :key="link.label">
                                <Button v-if="link.url" :variant="link.active ? 'default' : 'outline'" size="sm" as-child>
                                    <Link :href="link.url">{{ link.label }}</Link>
                                </Button>
                                <Button v-else variant="outline" size="sm" disabled>
                                    {{ link.label }}
                                </Button>
                            </template>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Dialog :open="userToDelete !== null" @update:open="(open) => (userToDelete = open ? userToDelete : null)">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Delete user</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to delete "{{ userToDelete?.name }}"? This action cannot be undone.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="userToDelete = null">Cancel</Button>
                        <Button variant="destructive" @click="destroy">Delete</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
