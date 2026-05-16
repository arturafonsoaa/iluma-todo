<script setup lang="ts">
import { ref } from 'vue';
import { Plus } from 'lucide-vue-next';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import TaskForm from '@/components/TaskForm.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';
import { toast } from 'vue-sonner';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const dialogOpen = ref(false);

function handleTaskSubmit() {
    dialogOpen.value = false;
    toast.success('Tarefa adicionada com sucesso!');
}

function handleTaskCancel() {
    dialogOpen.value = false;
}
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <Button
            variant="outline"
            size="sm"
            class="gap-2"
            @click="dialogOpen = true"
        >
            <Plus class="size-4" />
            <span>Adicionar tarefa</span>
        </Button>
    </header>

    <Dialog :open="dialogOpen" @update:open="dialogOpen = $event">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Nova Tarefa</DialogTitle>
                <DialogDescription>
                    Preencha os dados para criar uma nova tarefa.
                </DialogDescription>
            </DialogHeader>
            <TaskForm @submit="handleTaskSubmit" @cancel="handleTaskCancel" />
        </DialogContent>
    </Dialog>
</template>
