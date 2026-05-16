<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    CheckCircle2,
    Clock,
    AlertTriangle,
    TrendingUp,
    TrendingDown,
    Flag,
    CalendarDays,
    ChevronRight,
    ListTodo,
    Target,
    Zap,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import TaskDetailSheet from '@/pages/tasks/components/TaskDetailSheet.vue';
import { dashboard } from '@/routes';

interface Task {
    id: number;
    title: string;
    priority: string;
    priority_label: string;
    due_date: string | null;
    completed_at: string | null;
    created_at: string;
    is_completed: boolean;
    user: {
        name: string;
    };
}

interface Props {
    stats: {
        total: number;
        pending: number;
        completed: number;
        overdue: number;
        completionRate: number;
        weeklyCompleted: number;
        weeklyTrend: number;
    };
    priorityBreakdown: {
        low: number;
        medium: number;
        high: number;
        urgent: number;
    };
    dailyActivity: {
        labels: string[];
        completed: number[];
        created: number[];
    };
    todayTasks: Task[];
    upcomingTasks: Task[];
    recentTasks: Task[];
}

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const hoveredBar = ref<number | null>(null);
const selectedTask = ref<Task | null>(null);
const sheetOpen = ref(false);

function openTaskDetail(task: Task) {
    selectedTask.value = task;
    sheetOpen.value = true;
}

const priorityConfig = {
    low: {
        label: 'Baixa',
        color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        dot: 'bg-emerald-500',
        border: 'border-emerald-500',
    },
    medium: {
        label: 'Média',
        color: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        dot: 'bg-amber-500',
        border: 'border-amber-500',
    },
    high: {
        label: 'Alta',
        color: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
        dot: 'bg-orange-500',
        border: 'border-orange-500',
    },
    urgent: {
        label: 'Urgente',
        color: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        dot: 'bg-red-500',
        border: 'border-red-500',
    },
};

const maxPriorityCount = computed(() => Math.max(...Object.values(props.priorityBreakdown), 1));

const maxDailyCount = computed(() => {
    const allValues = [...props.dailyActivity.completed, ...props.dailyActivity.created];

    return Math.max(...allValues, 1);
});

const circumference = 2 * Math.PI * 54;
const strokeDashoffset = computed(() => {
    return circumference - (props.stats.completionRate / 100) * circumference;
});

function formatFullDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' });
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:gap-8 md:p-6 lg:p-8">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-foreground">
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-muted-foreground">
                Acompanhe seu progresso e produtividade
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Total Tasks -->
            <div class="group relative overflow-hidden rounded-xl border bg-card p-6 shadow-sm transition-all hover:shadow-md">
                <div class="absolute right-0 top-0 size-32 -translate-y-8 translate-x-8 rounded-full bg-blue-500/5 transition-transform group-hover:scale-110" />
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-muted-foreground">Total de Tarefas</p>
                        <div class="flex size-9 items-center justify-center rounded-lg bg-blue-500/10">
                            <ListTodo class="size-4 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-bold tracking-tight text-foreground">{{ stats.total }}</p>
                        <div class="mt-1 flex items-center gap-1.5">
                            <div class="flex h-1.5 flex-1 overflow-hidden rounded-full bg-muted">
                                <div
                                    class="rounded-full bg-blue-500 transition-all duration-500"
                                    :style="{ width: `${stats.total > 0 ? (stats.pending / stats.total) * 100 : 0}%` }"
                                />
                            </div>
                            <span class="text-xs text-muted-foreground">{{ stats.pending }} pendentes</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="group relative overflow-hidden rounded-xl border bg-card p-6 shadow-sm transition-all hover:shadow-md">
                <div class="absolute right-0 top-0 size-32 -translate-y-8 translate-x-8 rounded-full bg-emerald-500/5 transition-transform group-hover:scale-110" />
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-muted-foreground">Concluídas</p>
                        <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-500/10">
                            <CheckCircle2 class="size-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-bold tracking-tight text-foreground">{{ stats.completed }}</p>
                        <div class="mt-1 flex items-center gap-1">
                            <div
                                class="flex items-center gap-0.5 text-xs font-medium"
                                :class="stats.weeklyTrend >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
                            >
                                <component :is="stats.weeklyTrend >= 0 ? TrendingUp : TrendingDown" class="size-3" />
                                <span>{{ stats.weeklyTrend >= 0 ? '+' : '' }}{{ stats.weeklyTrend }}%</span>
                            </div>
                            <span class="text-xs text-muted-foreground">vs semana anterior</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completion Rate -->
            <div class="group relative overflow-hidden rounded-xl border bg-card p-6 shadow-sm transition-all hover:shadow-md">
                <div class="absolute right-0 top-0 size-32 -translate-y-8 translate-x-8 rounded-full bg-violet-500/5 transition-transform group-hover:scale-110" />
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-muted-foreground">Taxa de Conclusão</p>
                        <div class="flex size-9 items-center justify-center rounded-lg bg-violet-500/10">
                            <Target class="size-4 text-violet-600 dark:text-violet-400" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-4">
                        <div class="relative size-16">
                            <svg class="size-16 -rotate-90" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="54" fill="none" stroke="currentColor" stroke-width="8" class="text-muted/40" />
                                <circle
                                    cx="60"
                                    cy="60"
                                    r="54"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="8"
                                    stroke-linecap="round"
                                    class="text-violet-500"
                                    :style="{ strokeDasharray: circumference, strokeDashoffset }"
                                />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-bold text-foreground">{{ stats.completionRate }}%</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-muted-foreground">{{ stats.completed }} de {{ stats.total }} concluídas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue -->
            <div class="group relative overflow-hidden rounded-xl border bg-card p-6 shadow-sm transition-all hover:shadow-md">
                <div class="absolute right-0 top-0 size-32 -translate-y-8 translate-x-8 rounded-full bg-red-500/5 transition-transform group-hover:scale-110" />
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-muted-foreground">Atrasadas</p>
                        <div class="flex size-9 items-center justify-center rounded-lg" :class="stats.overdue > 0 ? 'bg-red-500/10' : 'bg-muted/50'">
                            <AlertTriangle :class="['size-4', stats.overdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-muted-foreground']" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-bold tracking-tight" :class="stats.overdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-foreground'">
                            {{ stats.overdue }}
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            <span v-if="stats.overdue > 0" class="text-red-600/80 dark:text-red-400/80 font-medium">Requer atenção imediata</span>
                            <span v-else>Tudo em dia</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Daily Activity Chart -->
            <div class="rounded-xl border bg-card p-6 shadow-sm lg:col-span-2">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-foreground">Atividade Semanal</h3>
                        <p class="text-sm text-muted-foreground">Tarefas criadas vs concluídas</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="size-2.5 rounded-full bg-chart-1" />
                            <span class="text-muted-foreground">Criadas</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="size-2.5 rounded-full bg-chart-2" />
                            <span class="text-muted-foreground">Concluídas</span>
                        </div>
                    </div>
                </div>

                <!-- Chart Area -->
                <div class="relative">
                    <!-- Grid Lines -->
                    <div class="absolute inset-0 flex flex-col justify-between pb-6 pt-0">
                        <div v-for="i in 4" :key="i" class="border-b border-dashed border-muted/50" />
                    </div>

                    <div class="relative flex h-56 items-end gap-3">
                        <div
                            v-for="(label, index) in dailyActivity.labels"
                            :key="label"
                            class="group/bar flex flex-1 flex-col items-center gap-2"
                            @mouseenter="hoveredBar = index"
                            @mouseleave="hoveredBar = null"
                        >
                            <!-- Tooltip -->
                            <div
                                v-if="hoveredBar === index && (dailyActivity.created[index] > 0 || dailyActivity.completed[index] > 0)"
                                class="absolute -top-12 z-10 rounded-lg bg-popover px-3 py-2 text-xs shadow-lg border border-border"
                            >
                                <p class="font-medium text-popover-foreground">{{ label }}</p>
                                <p class="text-muted-foreground">{{ dailyActivity.created[index] }} criadas, {{ dailyActivity.completed[index] }} concluídas</p>
                            </div>

                            <div class="flex w-full items-end justify-center gap-1.5">
                                <div
                                    class="w-3 rounded-t-md bg-chart-1 transition-all duration-200"
                                    :class="hoveredBar === index ? 'opacity-100' : 'opacity-80'"
                                    :style="{ height: `${Math.max((dailyActivity.created[index] / maxDailyCount) * 180, dailyActivity.created[index] > 0 ? 4 : 0)}px` }"
                                />
                                <div
                                    class="w-3 rounded-t-md bg-chart-2 transition-all duration-200"
                                    :class="hoveredBar === index ? 'opacity-100' : 'opacity-80'"
                                    :style="{ height: `${Math.max((dailyActivity.completed[index] / maxDailyCount) * 180, dailyActivity.completed[index] > 0 ? 4 : 0)}px` }"
                                />
                            </div>
                            <span class="text-xs font-medium text-muted-foreground">{{ label }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Priority Breakdown -->
            <div class="rounded-xl border bg-card p-6 shadow-sm">
                <div class="mb-6">
                    <h3 class="text-base font-semibold text-foreground">Por Prioridade</h3>
                    <p class="text-sm text-muted-foreground">Distribuição das tarefas</p>
                </div>

                <div class="space-y-5">
                    <div
                        v-for="(count, key) in priorityBreakdown"
                        :key="key"
                        class="group/priority space-y-2"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div :class="['size-2 rounded-sm transition-transform group-hover/priority:scale-125', priorityConfig[key].dot]" />
                                <span class="text-sm font-medium text-foreground">{{ priorityConfig[key].label }}</span>
                            </div>
                            <span class="text-sm font-semibold text-foreground">{{ count }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-muted/50">
                            <div
                                class="h-full rounded-full transition-all duration-700 ease-out"
                                :class="priorityConfig[key].dot"
                                :style="{ width: `${(count / maxPriorityCount) * 100}%` }"
                            />
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="mt-6 rounded-lg bg-muted/30 p-3">
                    <div class="flex items-center gap-2">
                        <Zap class="size-3.5 text-amber-500" />
                        <span class="text-xs font-medium text-foreground">
                            {{ priorityBreakdown.urgent + priorityBreakdown.high }} tarefas de alta prioridade
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Lists Row -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Today's Tasks -->
            <div class="rounded-xl border bg-card shadow-sm">
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-blue-500/10">
                            <CalendarDays class="size-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-foreground">Hoje</h3>
                            <p class="text-xs text-muted-foreground">{{ todayTasks.length }} tarefa{{ todayTasks.length !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <div v-if="todayTasks.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="mb-3 flex size-12 items-center justify-center rounded-full bg-muted/50">
                            <CheckCircle2 class="size-6 text-muted-foreground/40" />
                        </div>
                        <p class="text-sm font-medium text-foreground">Nenhuma tarefa para hoje</p>
                        <p class="mt-0.5 text-xs text-muted-foreground">Aproveite para organizar suas pendências</p>
                    </div>
                    <div v-else class="space-y-0.5">
                        <div
                            v-for="task in todayTasks"
                            :key="task.id"
                            class="flex items-start gap-3 rounded-lg p-3 transition-colors hover:bg-accent/50 cursor-pointer"
                            @click="openTaskDetail(task)"
                        >
                            <div :class="['mt-1.5 size-2 shrink-0 rounded-full', priorityConfig[task.priority].dot]" />
                            <div class="flex-1">
                                <p
                                    class="text-sm font-medium leading-snug"
                                    :class="task.is_completed ? 'text-muted-foreground line-through' : 'text-foreground'"
                                >
                                    {{ task.title }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Tasks -->
            <div class="rounded-xl border bg-card shadow-sm">
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-amber-500/10">
                            <Clock class="size-4 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-foreground">Próximos 7 Dias</h3>
                            <p class="text-xs text-muted-foreground">{{ upcomingTasks.length }} tarefa{{ upcomingTasks.length !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <div v-if="upcomingTasks.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="mb-3 flex size-12 items-center justify-center rounded-full bg-muted/50">
                            <CalendarDays class="size-6 text-muted-foreground/40" />
                        </div>
                        <p class="text-sm font-medium text-foreground">Nenhuma tarefa próxima</p>
                        <p class="mt-0.5 text-xs text-muted-foreground">Sua agenda está livre esta semana</p>
                    </div>
                    <div v-else class="space-y-0.5">
                        <div
                            v-for="task in upcomingTasks"
                            :key="task.id"
                            class="flex items-start gap-3 rounded-lg p-3 transition-colors hover:bg-accent/50 cursor-pointer"
                            @click="openTaskDetail(task)"
                        >
                            <div :class="['mt-1.5 size-2 shrink-0 rounded-full', priorityConfig[task.priority].dot]" />
                            <div class="flex-1 space-y-1.5">
                                <p class="text-sm font-medium leading-snug text-foreground">
                                    {{ task.title }}
                                </p>
                                <span class="text-xs text-muted-foreground">
                                    {{ task.due_date ? formatFullDate(task.due_date) : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="rounded-xl border bg-card shadow-sm">
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-violet-500/10">
                            <Flag class="size-4 text-violet-600 dark:text-violet-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-foreground">Recentes</h3>
                            <p class="text-xs text-muted-foreground">Últimas atividades</p>
                        </div>
                    </div>
                    <Link href="/tasks" class="flex items-center gap-0.5 text-xs font-medium text-muted-foreground transition-colors hover:text-foreground">
                        Ver todas
                        <ChevronRight class="size-3" />
                    </Link>
                </div>
                <div class="p-2">
                    <div v-if="recentTasks.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="mb-3 flex size-12 items-center justify-center rounded-full bg-muted/50">
                            <ListTodo class="size-6 text-muted-foreground/40" />
                        </div>
                        <p class="text-sm font-medium text-foreground">Nenhuma tarefa ainda</p>
                        <p class="mt-0.5 text-xs text-muted-foreground">Crie sua primeira tarefa para começar</p>
                    </div>
                    <div v-else class="space-y-0.5">
                        <div
                            v-for="task in recentTasks"
                            :key="task.id"
                            class="flex items-start gap-3 rounded-lg p-3 transition-colors hover:bg-accent/50 cursor-pointer"
                            @click="openTaskDetail(task)"
                        >
                            <CheckCircle2
                                v-if="task.is_completed"
                                class="mt-0.5 size-4 shrink-0 text-emerald-500"
                            />
                            <div
                                v-else
                                :class="['mt-0.5 size-4 shrink-0 rounded-full border-2', priorityConfig[task.priority].dot.replace('bg-', 'border-')]"
                            />
                            <div class="flex-1 space-y-1.5">
                                <p
                                    class="text-sm font-medium leading-snug"
                                    :class="task.is_completed ? 'text-muted-foreground line-through' : 'text-foreground'"
                                >
                                    {{ task.title }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <Badge :class="['text-[10px]', priorityConfig[task.priority].color]">
                                        {{ task.priority_label }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">
                                        {{ formatFullDate(task.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Detail Sheet -->
    <TaskDetailSheet
        v-if="selectedTask"
        :task="{
            id: selectedTask.id,
            title: selectedTask.title,
            priority: selectedTask.priority as 'low' | 'medium' | 'high' | 'urgent',
            due_date: selectedTask.due_date,
            completed_at: selectedTask.completed_at,
            created_at: selectedTask.created_at,
            user: selectedTask.user,
        }"
        :open="sheetOpen"
        @update:open="sheetOpen = $event"
    />
</template>
