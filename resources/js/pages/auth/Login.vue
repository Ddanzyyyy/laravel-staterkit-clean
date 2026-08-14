<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthLayout title="Welcome back" description="Enter your email and password below to log in">
        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="grid gap-2">
            
                <Label for="email">Email address</Label>
                <Input id="email" type="email" required autofocus :tabindex="1" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                <InputError :message="form.errors.email" />
            </div>

            <div class="mt-4 grid gap-2">
                <div class="flex items-center">
                    <Label for="password">Password</Label>
                    <TextLink v-if="canResetPassword" :href="route('password.request')" class="ml-auto text-sm" :tabindex="5">
                        Forgot password?
                    </TextLink>
                </div>
                <Input id="password" type="password" required :tabindex="2" autocomplete="current-password" v-model="form.password" placeholder="Password" />
                <InputError :message="form.errors.password" />
            </div>

            <div class="mt-4 flex items-center space-x-3">
                <Checkbox id="remember" v-model:checked="form.remember" name="remember" :tabindex="3" />
                <Label for="remember">Remember me</Label>
            </div>

            <div class="mb-4 mt-6 flex items-center justify-start">
                <Button class="w-full" :disabled="form.processing" :tabindex="4">
                
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-muted-foreground">
            <span>Don't have an account?</span>
            <TextLink :href="route('register')" :tabindex="6">Sign up</TextLink>
        </div>
    </AuthLayout>
</template>
