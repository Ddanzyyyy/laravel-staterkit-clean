<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <AuthLayout title="Create an account" description="Enter your details below to create your account">
        <form @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="mt-4 grid gap-2">
                <Label for="email">Email address</Label>
                <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                <InputError :message="form.errors.email" />
            </div>

            <div class="mt-4 grid gap-2">
                <Label for="password">Password</Label>
                <Input id="password" type="password" required :tabindex="3" autocomplete="new-password" v-model="form.password" placeholder="Password" />
                <InputError :message="form.errors.password" />
            </div>

            <div class="mt-4 grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <Input id="password_confirmation" type="password" required :tabindex="4" autocomplete="new-password" v-model="form.password_confirmation" placeholder="Confirm password" />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <div class="mb-4 mt-6 flex items-center justify-start">
                <Button class="w-full" :disabled="form.processing" :tabindex="5">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-muted-foreground">
            <span>Already have an account?</span>
            <TextLink :href="route('login')" :tabindex="6">Log in</TextLink>
        </div>
    </AuthLayout>
</template>
