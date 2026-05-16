<script setup lang="ts">
import { cn } from '@/lib/utils';

defineProps<{
    dotClass: string;
    class?: string;
}>();
</script>

<template>
    <span
        :class="cn('size-3 shrink-0 rounded-full', dotClass, $props.class)"
        aria-hidden="true"
    />
</template>
