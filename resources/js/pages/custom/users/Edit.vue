<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Pencil } from 'lucide-vue-next';
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
    {
        title: 'Edit User',
        href: `/users/${'id'}/edit`,
    },
];

const props = defineProps<{
    user: User;
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('users.update', { id: props.user.id }), {
        onSuccess: () => toast.success('User updated successfully'),
        onError: () => toast.error('Failed to update user'),
    });
};
</script>

<template>
    <Head title="Edit User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="max-w-lg">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Pencil class="h-5 w-5" />
                        Edit User
                    </CardTitle>
                    <CardDescription>Update user details. Leave password blank to keep it unchanged.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" type="text" required autofocus autocomplete="name" v-model="form.name" placeholder="Full name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <Input id="email" type="email" required autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">New password</Label>
                            <Input id="password" type="password" autocomplete="new-password" v-model="form.password" placeholder="Leave blank to keep unchanged" />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirm new password</Label>
                            <Input id="password_confirmation" type="password" autocomplete="new-password" v-model="form.password_confirmation" placeholder="Confirm new password" />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

                        <Button type="submit" class="w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Update user
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
