<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ListTodo, Plus, Trash2, CalendarDays, Flag, CheckCircle2, CircleDot, ChevronLeft, ChevronRight, RotateCcw, Play } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';
import { toast } from 'vue-sonner';
import TaskDetailSheet from '@/components/TaskDetailSheet.vue';
import TaskForm from '@/components/TaskForm.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { index as tasksIndex } from '@/routes/tasks';

interface Task {
    id: number;
    title: string;
    due_date: string | null;
    completed_at: string | null;
    created_at: string;
    priority: 'low' | 'medium' | 'high' | 'urgent';
    status: string;
    started_at: string | null;
    user: {
        name: string;
    };
}

interface PaginatorLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTasks {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginatorLink[];
    prev_page_url: string | null;
    next_page_url: string | null;
    data: Task[];
}

const props = defineProps<{
    tasks: PaginatedTasks;
    filter?: 'pending' | 'completed' | 'trash';
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
const localFilter = ref<'pending' | 'completed' | 'trash'>(props.filter ?? 'pending');
const localTasks = ref<Task[]>([...props.tasks.data]);
const localPaginator = ref<PaginatedTasks>(props.tasks);
const dialogOpen = ref(false);

const notStartedTasks = computed(() =>
    localTasks.value.filter((t) => t.status !== 'in_progress'),
);

const inProgressTasks = computed(() =>
    localTasks.value.filter((t) => t.status === 'in_progress'),
);

watch(
    () => props.tasks,
    (newTasks) => {
        localTasks.value = [...newTasks.data];
        localPaginator.value = newTasks;
        fadingTaskIds.value.clear();

        if (selectedTask.value) {
            const updated = newTasks.data.find((t) => t.id === selectedTask.value!.id);
            if (updated) {
                selectedTask.value = updated;
            }
        }
    },
);

watch(
    () => props.filter,
    (newFilter) => {
        if (newFilter) {
            localFilter.value = newFilter;
        }
    },
);

function setFilter(filter: 'pending' | 'completed' | 'trash') {
    localFilter.value = filter;
    router.get(
        tasksIndex().url,
        { filter },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}

function getPageFromUrl(url: string | null): number | null {
    if (!url) {
return null;
}

    const match = url.match(/[?&]page=(\d+)/);

    return match ? parseInt(match[1], 10) : null;
}

function goToPage(url: string | null) {
    const page = getPageFromUrl(url);

    if (page) {
        router.get(
            tasksIndex().url,
            { filter: localFilter.value, page },
            { preserveScroll: true, preserveState: true, replace: true },
        );
    }
}

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

function startTask(task: Task, event: Event) {
    event.stopPropagation();
    fadingTaskIds.value.add(task.id);

    setTimeout(() => {
        const taskIndex = localTasks.value.findIndex((t) => t.id === task.id);

        if (taskIndex !== -1) {
            localTasks.value[taskIndex] = {
                ...localTasks.value[taskIndex],
                status: 'in_progress',
                started_at: new Date().toISOString(),
            };
        }

        fadingTaskIds.value.delete(task.id);
    }, 1000);

    router.patch(
        `/tasks/${task.id}/start`,
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

function handleTaskSubmit() {
    dialogOpen.value = false;
    toast.success('Tarefa adicionada com sucesso!');
}

function restoreTask(task: Task, event: Event) {
    event.stopPropagation();
    fadingTaskIds.value.add(task.id);

    setTimeout(() => {
        localTasks.value = localTasks.value.filter((t) => t.id !== task.id);
        fadingTaskIds.value.delete(task.id);
    }, 1000);

    router.post(
        `/tasks/${task.id}/restore`,
        {},
        {
            preserveScroll: true,
        },
    );
}

function renderTaskSection(tasks: Task[], sectionTitle: string, emptyMessage: string) {
    return { tasks, sectionTitle, emptyMessage };
}
</script>

<template>
    <Head title="Tarefas" />

    <div class="flex h-full flex-1 flex-col gap-8 overflow-x-auto p-4 md:p-8">
        <div class="mx-auto w-full max-w-3xl space-y-8">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <h1 class="text-3xl font-semibold tracking-tight text-foreground">
                        Minhas Tarefas
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Organize seu dia, uma tarefa de cada vez
                    </p>
                </div>

                <Dialog v-model:open="dialogOpen">
                    <DialogTrigger as-child>
                        <Button variant="outline">
                            <Plus class="size-4" />
                            <span>Adicionar tarefa</span>
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Nova Tarefa</DialogTitle>
                            <DialogDescription>
                                Preencha os dados para criar uma nova tarefa.
                            </DialogDescription>
                        </DialogHeader>
                        <TaskForm @submit="handleTaskSubmit" @cancel="dialogOpen = false" />
                    </DialogContent>
                </Dialog>
            </div>

            <div class="flex items-center justify-between">
                <div class="inline-flex rounded-lg border bg-muted/50 p-1">
                    <button
                        type="button"
                        @click="setFilter('pending')"
                        class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-all duration-200"
                        :class="localFilter === 'pending'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'"
                    >
                        <CircleDot class="size-4" />
                        Pendentes
                    </button>
                    <button
                        type="button"
                        @click="setFilter('completed')"
                        class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-all duration-200"
                        :class="localFilter === 'completed'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'"
                    >
                        <CheckCircle2 class="size-4" />
                        Concluídas
                    </button>
                    <button
                        type="button"
                        @click="setFilter('trash')"
                        class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-all duration-200"
                        :class="localFilter === 'trash'
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'"
                    >
                        <Trash2 class="size-4" />
                        Lixeira
                    </button>
                </div>

                <span class="text-sm text-muted-foreground">
                    {{ localPaginator.total }} {{ localPaginator.total === 1 ? 'tarefa' : 'tarefas' }}
                </span>
            </div>

            <div v-if="localTasks.length === 0" class="py-16 text-center">
                <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-muted">
                    <ListTodo class="size-8 text-muted-foreground" />
                </div>
                <h3 class="mt-4 text-lg font-medium text-foreground">
                    {{ localFilter === 'pending' ? 'Nenhuma tarefa pendente' : localFilter === 'completed' ? 'Nenhuma tarefa concluída' : 'Lixeira vazia' }}
                </h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    {{ localFilter === 'pending' ? 'Crie sua primeira tarefa acima para começar.' : localFilter === 'completed' ? 'Complete uma tarefa para vê-la aqui.' : 'Tarefas excluídas aparecerão aqui.' }}
                </p>
            </div>

            <template v-else-if="localFilter === 'pending'">
                <div v-if="notStartedTasks.length > 0" class="space-y-2">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        Não iniciadas
                    </h2>
                    <ul class="space-y-2">
                        <li
                            v-for="task in notStartedTasks"
                            :key="task.id"
                            class="group cursor-pointer"
                            @click="openTaskSheet(task)"
                        >
                            <div
                                class="flex items-center gap-3 rounded-lg border bg-card px-4 py-3 transition-all duration-1000 hover:shadow-md"
                                :class="[
                                    { 'opacity-0 scale-95 -translate-x-4': fadingTaskIds.has(task.id) },
                                ]"
                            >
                                <button
                                    type="button"
                                    class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 transition-all duration-200"
                                    :class="task.completed_at
                                        ? 'border-emerald-500 bg-emerald-500 text-white dark:border-emerald-400 dark:bg-emerald-400'
                                        : 'border-muted-foreground/30 hover:border-primary hover:bg-primary/5'"
                                    @click.stop="completeTask(task, $event)"
                                >
                                    <svg v-if="task.completed_at" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    class="flex size-6 shrink-0 items-center justify-center rounded-md text-muted-foreground transition-all duration-200 hover:bg-blue-500/10 hover:text-blue-500 dark:hover:bg-blue-500/20"
                                    @click.stop="startTask(task, $event)"
                                >
                                    <Play class="size-3.5" />
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
                </div>

                <div v-if="inProgressTasks.length > 0" class="mt-6 space-y-2">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        Iniciadas
                    </h2>
                    <ul class="space-y-2">
                        <li
                            v-for="task in inProgressTasks"
                            :key="task.id"
                            class="group cursor-pointer"
                            @click="openTaskSheet(task)"
                        >
                            <div
                                class="flex items-center gap-3 rounded-lg border bg-card px-4 py-3 transition-all duration-1000 hover:shadow-md"
                                :class="[
                                    { 'opacity-0 scale-95 -translate-x-4': fadingTaskIds.has(task.id) },
                                ]"
                            >
                                <button
                                    type="button"
                                    class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 transition-all duration-200"
                                    :class="task.completed_at
                                        ? 'border-emerald-500 bg-emerald-500 text-white dark:border-emerald-400 dark:bg-emerald-400'
                                        : 'border-muted-foreground/30 hover:border-primary hover:bg-primary/5'"
                                    @click.stop="completeTask(task, $event)"
                                >
                                    <svg v-if="task.completed_at" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>

                                <span
                                    class="flex size-6 shrink-0 items-center justify-center rounded-md bg-blue-500/10 text-blue-500 dark:bg-blue-500/20"
                                >
                                    <Play class="size-3.5 fill-blue-500" />
                                </span>

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
                </div>
            </template>

            <ul v-else class="space-y-2">
                <li
                    v-for="task in localTasks"
                    :key="task.id"
                    class="group cursor-pointer"
                    @click="openTaskSheet(task)"
                >
                    <div
                        class="flex items-center gap-3 rounded-lg border bg-card px-4 py-3 transition-all duration-1000 hover:shadow-md"
                        :class="[
                            { 'opacity-0 scale-95 -translate-x-4': fadingTaskIds.has(task.id) },
                            task.completed_at ? 'opacity-75' : ''
                        ]"
                    >
                        <button
                            type="button"
                            class="flex size-6 shrink-0 items-center justify-center rounded-full border-2 transition-all duration-200"
                            :class="task.completed_at
                                ? 'border-emerald-500 bg-emerald-500 text-white dark:border-emerald-400 dark:bg-emerald-400'
                                : 'border-muted-foreground/30 hover:border-primary hover:bg-primary/5'"
                            @click.stop="completeTask(task, $event)"
                        >
                            <svg v-if="task.completed_at" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </button>

                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            <span
                                class="flex-1 truncate text-sm font-medium"
                                :class="task.completed_at
                                    ? 'text-muted-foreground line-through'
                                    : 'text-foreground'"
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

                        <div v-if="localFilter === 'trash'" class="shrink-0">
                            <button
                                type="button"
                                class="rounded-md p-1.5 text-muted-foreground opacity-0 transition-all duration-200 hover:bg-emerald-500/10 hover:text-emerald-500 group-hover:opacity-100"
                                @click.stop="restoreTask(task, $event)"
                            >
                                <RotateCcw class="size-4" />
                            </button>
                        </div>
                        <Link
                            v-else
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

            <div v-if="localPaginator.last_page > 1" class="flex items-center justify-between gap-4">
                <p class="text-sm text-muted-foreground">
                    Mostrando {{ localPaginator.from }} a {{ localPaginator.to }} de {{ localPaginator.total }} resultados
                </p>

                <nav class="inline-flex items-center gap-1">
                    <button
                        type="button"
                        @click="goToPage(localPaginator.prev_page_url)"
                        :disabled="!localPaginator.prev_page_url"
                        class="inline-flex size-8 items-center justify-center rounded-md border bg-background text-sm transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-50"
                    >
                        <ChevronLeft class="size-4" />
                    </button>

                    <template v-for="(link, index) in localPaginator.links" :key="index">
                        <button
                            v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                            type="button"
                            @click="goToPage(link.url)"
                            class="inline-flex size-8 items-center justify-center rounded-md border text-sm transition-colors"
                            :class="link.active
                                ? 'border-primary bg-primary text-primary-foreground hover:bg-primary/90'
                                : 'bg-background hover:bg-muted'"
                        >
                            {{ link.label.replace('&laquo;', '«').replace('&raquo;', '»') }}
                        </button>
                    </template>

                    <button
                        type="button"
                        @click="goToPage(localPaginator.next_page_url)"
                        :disabled="!localPaginator.next_page_url"
                        class="inline-flex size-8 items-center justify-center rounded-md border bg-background text-sm transition-colors hover:bg-muted disabled:pointer-events-none disabled:opacity-50"
                    >
                        <ChevronRight class="size-4" />
                    </button>
                </nav>
            </div>

            <TaskDetailSheet
                :task="selectedTask"
                :open="sheetOpen"
                @update:open="sheetOpen = $event"
            />
        </div>
    </div>
</template>
