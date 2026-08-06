<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, LayoutGrid } from 'lucide-vue-next';

const page = usePage<{ auth: { user: unknown | null } }>();
const user = page.props.auth.user;
</script>

<template>
    <Head title="Welcome" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-background p-6">
        <main class="flex w-full max-w-md flex-col items-center gap-6 text-center">
            <h1 class="text-4xl font-bold tracking-tight">Welcome</h1>
            <p class="text-muted-foreground">Your starter kit is ready. Explore the dashboard or create an account.</p>

            <div class="flex gap-3">
                <Button v-if="user" as-child>
                    <Link :href="route('dashboard')">
                        <LayoutGrid class="h-4 w-4" />
                        Dashboard
                    </Link>
                </Button>
                <template v-else>
                    <Button as-child>
                        <Link :href="route('login')">
                            Log in
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('register')">Register</Link>
                    </Button>
                </template>
            </div>
        </main>
    </div>
</template>
