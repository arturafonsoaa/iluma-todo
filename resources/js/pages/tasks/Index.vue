<script setup lang="ts">
import { Head, Link, Form } from '@inertiajs/vue3';
import { ListTodo, Plus, Trash2, Check, CalendarDays, Flag } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index as tasksIndex } from '@/routes/tasks';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tasks',
                href: tasksIndex(),
            },
        ],
    },
});

interface Task {
    id: number;
    title: string;
    due_date: string | null;
    completed_at: string | null;
    created_at: string;
    priority: 'low' | 'medium' | 'high' | 'urgent';
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('pt-BR');
}

function getPriorityColor(priority: string): string {
    return {
        low: 'text-green-500',
        medium: 'text-yellow-500',
        high: 'text-orange-500',
        urgent: 'text-red-500',
    }[priority] || 'text-muted-foreground';
}

defineProps<{
    tasks: Task[];
}>();
</script>

<template>
    <Head title="Tasks" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="mx-auto w-full max-w-2xl space-y-6">
            <Form
                action="/tasks"
                method="post"
                reset-on-success
                v-slot="{ errors, processing }"
                class="flex gap-3"
            >
                <div class="flex-1 space-y-1">
                    <Input
                        type="text"
                        name="title"
                        placeholder="What needs to be done?"
                        class="w-full"
                        required
                    />
                    <div class="flex gap-2">
                        <Input
                            type="date"
                            name="due_date"
                            class="w-full"
                        />
                        <select
                            name="priority"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    <InputError :message="errors.title" />
                </div>
                <Button type="submit" :disabled="processing" class="shrink-0">
                    <Plus class="size-4" />
                    Add
                </Button>
            </Form>

            <div v-if="tasks.length === 0" class="py-12 text-center">
                <ListTodo class="mx-auto size-12 text-muted-foreground" />
                <p class="mt-4 text-sm text-muted-foreground">No tasks yet. Create your first task above.</p>
            </div>

            <ul v-else class="space-y-1">
                <li
                    v-for="task in tasks"
                    :key="task.id"
                    class="group flex items-center gap-3 rounded-lg border px-3 py-2.5"
                    :class="task.completed_at ? 'bg-muted/50' : 'bg-card'"
                >
                    <Link
                        :href="`/tasks/${task.id}`"
                        method="patch"
                        as="button"
                        class="flex size-5 shrink-0 items-center justify-center rounded-full border-2 transition-colors"
                        :class="task.completed_at
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-muted-foreground/30 hover:border-primary'"
                    >
                        <Check v-if="task.completed_at" class="size-3" />
                    </Link>

                    <Flag class="size-4 shrink-0" :class="getPriorityColor(task.priority)" />

                    <span
                        class="flex-1 text-sm"
                        :class="task.completed_at && 'text-muted-foreground line-through'"
                    >
                        {{ task.title }}
                        <span
                            v-if="task.due_date"
                            class="ml-2 inline-flex items-center gap-1 text-xs text-muted-foreground"
                        >
                            <CalendarDays class="size-3" />
                            {{ formatDate(task.due_date) }}
                        </span>
                    </span>

                    <Link
                        :href="`/tasks/${task.id}`"
                        method="delete"
                        as="button"
                        class="invisible group-hover:visible shrink-0 rounded-md p-1 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                    >
                        <Trash2 class="size-4" />
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>
