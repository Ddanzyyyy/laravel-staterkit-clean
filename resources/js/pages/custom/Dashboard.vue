<script setup lang="ts">
import ProfileAvatar from '@/components/custom/ProfileAvatar.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { Activity, DollarSign, ShoppingCart, Users } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const page = usePage<SharedData & Record<string, unknown>>();
const user = page.props.auth.user;

// ponytail: static demo numbers — replace with real metrics when backend data exists
const stats = [
    { title: 'Total Customers', value: '1,204', icon: Users },
    { title: 'Total Orders', value: '356', icon: ShoppingCart },
    { title: 'Revenue', value: '$12,450', icon: DollarSign },
    { title: 'Active Users', value: '89', icon: Activity },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="p-4">
                <ProfileAvatar :user="user" />
            </Card>

            <div class="grid auto-rows-min gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card v-for="stat in stats" :key="stat.title">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">{{ stat.title }}</CardTitle>
                        <component :is="stat.icon" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stat.value }}</div>
                    </CardContent>
                </Card>
            </div>

            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="flex h-full items-center justify-center text-muted-foreground">
                    Chart placeholder
                </div>
            </div>
        </div>
    </AppLayout>
</template>
