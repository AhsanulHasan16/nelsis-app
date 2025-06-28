<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import type { User } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    user?: User | null;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    user: null,
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :user="user">
        <slot />
    </AppLayout>
</template>