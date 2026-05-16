<script setup lang="ts">
import { Head, Link, Form, router } from '@inertiajs/vue3';
import { ListTodo, Plus, Trash2, CalendarDays, Flag } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import TaskDetailSheet from '@/components/TaskDetailSheet.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index as tasksIndex } from '@/routes/tasks';

interface Task {
    id: number;
    title: string;
    due_date: string | null;
    completed_at: string | null;
    created_at: string;
    priority: 'low' | 'medium' | 'high' | 'urgent';
    user: {
        name: string;
    };
}

const props = defineProps<{
    tasks: Task[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tarefas',
                href: tasksIndex(),
            },
        ],
    },
});

const selectedTask = ref<Task | null>(null);
const sheetOpen = ref(false);
const fadingTaskIds = ref<Set<number>>(new Set());
const localTasks = ref<Task[]>([...props.tasks]);

watch(
    () => props.tasks,
    (newTasks) => {
        localTasks.value = [...newTasks];
        fadingTaskIds.value.clear();
    },
);

function openTaskSheet(task: Task) {
    selectedTask.value = task;
    sheetOpen.value = true;
}

function completeTask(task: Task, event: Event) {
    event.stopPropagation();
    fadingTaskIds.value.add(task.id);

    setTimeout(() => {
        localTasks.value = localTasks.value.filter((t) => t.id !== task.id);
        fadingTaskIds.value.delete(task.id);
    }, 1000);

    router.patch(
        `/tasks/${task.id}`,
        {},
        {
            preserveScroll: true,
        },
    );
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' });
}

function getPriorityColor(priority: string): { bg: string; text: string; border: string; badge: string } {
    const colors = {
        low: {
            bg: 'bg-emerald-50 dark:bg-emerald-950/30',
            text: 'text-emerald-700 dark:text-emerald-400',
            border: 'border-emerald-200 dark:border-emerald-800',
            badge: 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300',
        },
        medium: {
            bg: 'bg-amber-50 dark:bg-amber-950/30',
            text: 'text-amber-700 dark:text-amber-400',
            border: 'border-amber-200 dark:border-amber-800',
            badge: 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
        },
        high: {
            bg: 'bg-orange-50 dark:bg-orange-950/30',
            text: 'text-orange-700 dark:text-orange-400',
            border: 'border-orange-200 dark:border-orange-800',
            badge: 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300',
        },
        urgent: {
            bg: 'bg-red-50 dark:bg-red-950/30',
            text: 'text-red-700 dark:text-red-400',
            border: 'border-red-200 dark:border-red-800',
            badge: 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300',
        },
    };

    return colors[priority as keyof typeof colors] || colors.medium;
}

function getPriorityLabel(priority: string): string {
    return {
        low: 'Baixa',
        medium: 'Média',
        high: 'Alta',
        urgent: 'Urgente',
    }[priority] || priority;
}
</script>

<template>
    <Head title="Tarefas" />

    <div class="flex h-full flex-1 flex-col gap-8 overflow-x-auto p-4 md:p-8">
        <div class="mx-auto w-full max-w-3xl space-y-8">
            <div class="space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight text-foreground">
                    Minhas Tarefas
                </h1>
                <p class="text-sm text-muted-foreground">
                    Organize seu dia, uma tarefa de cada vez
                </p>
            </div>

            <Form
                action="/tasks"
                method="post"
                reset-on-success
                v-slot="{ errors, processing }"
                class="rounded-xl border bg-card p-4 shadow-sm"
            >
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-foreground">
                            Título da Tarefa
                        </label>
                        <Input
                            type="text"
                            name="title"
                            placeholder="O que precisa ser feito?"
                            class="w-full"
                            required
                        />
                        <InputError :message="errors.title" />
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1 space-y-2">
                            <label class="text-sm font-medium leading-none text-foreground">
                                Data de Vencimento
                            </label>
                            <Input
                                type="date"
                                name="due_date"
                                class="w-full"
                            />
                        </div>

                        <div class="flex-1 space-y-2">
                            <label class="text-sm font-medium leading-none text-foreground">
                                Prioridade
                            </label>
                            <select
                                name="priority"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="low">Baixa</option>
                                <option value="medium" selected>Média</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>

                        <Button type="submit" :disabled="processing" class="w-full sm:w-auto">
                            <Plus class="size-4" />
                            <span>{{ processing ? 'Adicionando...' : 'Adicionar Tarefa' }}</span>
                        </Button>
                    </div>
                </div>
            </Form>

            <div v-if="localTasks.length === 0" class="py-16 text-center">
                <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-muted">
                    <ListTodo class="size-8 text-muted-foreground" />
                </div>
                <h3 class="mt-4 text-lg font-medium text-foreground">Nenhuma tarefa ainda</h3>
                <p class="mt-2 text-sm text-muted-foreground">Crie sua primeira tarefa acima para começar.</p>
            </div>

            <ul v-else class="space-y-2">
                <li
                    v-for="task in localTasks"
                    :key="task.id"
                    class="group cursor-pointer"
                    @click="openTaskSheet(task)"
                >
                    <div
                        class="flex items-center gap-3 rounded-lg border bg-card px-4 py-3 transition-all duration-1000 hover:shadow-md"
                        :class="{ 'opacity-0 scale-95 -translate-x-4': fadingTaskIds.has(task.id) }"
                    >
                        <button
                            type="button"
                            class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 border-muted-foreground/30 transition-all duration-200 hover:border-primary hover:bg-primary/5"
                            @click.stop="completeTask(task, $event)"
                        >
                        </button>

                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            <span
                                class="flex-1 truncate text-sm font-medium text-foreground"
                            >
                                {{ task.title }}
                            </span>

                            <div class="flex shrink-0 items-center gap-2">
                                <span
                                    v-if="task.due_date"
                                    class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium text-muted-foreground bg-background/80 border"
                                >
                                    <CalendarDays class="size-3" />
                                    {{ formatDate(task.due_date) }}
                                </span>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium border"
                                    :class="[
                                        getPriorityColor(task.priority).badge,
                                        getPriorityColor(task.priority).border
                                    ]"
                                >
                                    <Flag class="size-3" />
                                    {{ getPriorityLabel(task.priority) }}
                                </span>
                            </div>
                        </div>

                        <Link
                            :href="`/tasks/${task.id}`"
                            method="delete"
                            as="button"
                            class="shrink-0 rounded-md p-1.5 text-muted-foreground opacity-0 transition-all duration-200 hover:bg-destructive/10 hover:text-destructive group-hover:opacity-100"
                            @click.stop
                        >
                            <Trash2 class="size-4" />
                        </Link>
                    </div>
                </li>
            </ul>

            <TaskDetailSheet
                :task="selectedTask"
                :open="sheetOpen"
                @update:open="sheetOpen = $event"
            />
        </div>
    </div>
</template>
