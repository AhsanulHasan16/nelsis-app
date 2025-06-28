<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const user = ref(null);
const error = ref('');
const router = useRouter();

onMounted(async () => {
    const token = localStorage.getItem('access_token');
    if (!token) {
        router.push('/login');
        return;
    }
    try {
        const res = await fetch('/api/me', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
            },
        });
        if (res.ok) {
            user.value = await res.json();
        } else {
            error.value = 'Unauthorized';
            router.push('/login');
        }
    } catch (e) {
        error.value = 'Failed to load dashboard data';
    }
});
</script>

<!-- <template>
    <Head title="Dashboard" />

    <AppLayout :user="user">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template> -->


<template>
    <Head title="Dashboard" />

    <AppLayout :user="user">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div v-if="!user && !error" class="flex items-center justify-center h-full">
                <span>Loading...</span>
            </div>
            <div v-else-if="error" class="flex items-center justify-center h-full text-red-500">
                {{ error }}
            </div>
            <div v-else>
                <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <div class="mx-7 my-7">{{ user.email }}</div>
                    </div>
                    <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <div class="mx-7 my-7">{{ user.name }}</div>
                    </div>
                    <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern />
                    </div>
                </div>
                <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border mt-4">
                    <PlaceholderPattern />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<!-- <template>
    <AppLayout :user="user">
    <div>
        <h1>Dashboard</h1>
        <div v-if="user">
            <p>Welcome, {{ user.name }}</p>
            <p>Email: {{ user.email }}</p>
        </div>
        <div v-else-if="error">
            <p>{{ error }}</p>
        </div>
        <div v-else>
            <p>Loading...</p>
        </div>
    </div>
</AppLayout>
</template> -->