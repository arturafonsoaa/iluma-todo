<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    CheckCircle2,
    Clock,
    AlertTriangle,
    TrendingUp,
    TrendingDown,
    Flag,
    CalendarDays,
    Plus,
    ChevronRight,
    ListTodo,
} from 'lucide-vue-next';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Task {
    id: number;
    title: string;
    priority: string;
    priority_label: string;
    due_date: string | null;
    completed_at: string | null;
    created_at: string;
    is_completed: boolean;
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

const priorityConfig = {
    low: {
        label: 'Baixa',
        color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        dot: 'bg-emerald-500',
    },
    medium: {
        label: 'Média',
        color: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        dot: 'bg-amber-500',
    },
    high: {
        label: 'Alta',
        color: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
        dot: 'bg-orange-500',
    },
    urgent: {
        label: 'Urgente',
        color: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        dot: 'bg-red-500',
    },
};

const maxPriorityCount = computed(() => {
    return Math.max(...Object.values(props.priorityBreakdown), 1);
});

const maxDailyCount = computed(() => {
    const allValues = [...props.dailyActivity.completed, ...props.dailyActivity.created];
    return Math.max(...allValues, 1);
});

const completionAngle = computed(() => {
    return (props.stats.completionRate / 100) * 360;
});

function getDaysRemaining(dueDate: string | null): string | null {
    if (!dueDate) return null;
    const due = new Date(dueDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    due.setHours(0, 0, 0, 0);
    const diff = Math.ceil((due.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
    if (diff < 0) return `${Math.abs(diff)}d atrasado`;
    if (diff === 0) return 'Hoje';
    if (diff === 1) return 'Amanhã';
    return `${diff}d restantes`;
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'short',
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:gap-8 md:p-8">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-foreground">
                    Dashboard
                </h1>
                <p class="text-sm text-muted-foreground">
                    Visão geral das suas tarefas e produtividade
                </p>
            </div>
            <Button as-child>
                <Link href="/tasks">
                    <Plus class="mr-2 size-4" />
                    Nova Tarefa
                </Link>
            </Button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Tasks -->
            <Card class="relative overflow-hidden">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Total de Tarefas
                    </CardTitle>
                    <ListTodo class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-foreground">{{ stats.total }}</div>
                    <p class="text-xs text-muted-foreground">
                        {{ stats.pending }} pendente{{ stats.pending !== 1 ? 's' : '' }}
                    </p>
                </CardContent>
            </Card>

            <!-- Completed -->
            <Card class="relative overflow-hidden">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Concluídas
                    </CardTitle>
                    <CheckCircle2 class="size-4 text-emerald-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-foreground">{{ stats.completed }}</div>
                    <div class="flex items-center gap-1 text-xs" :class="stats.weeklyTrend >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                        <component :is="stats.weeklyTrend >= 0 ? TrendingUp : TrendingDown" class="size-3" />
                        <span>{{ stats.weeklyCompleted }} esta semana</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Completion Rate -->
            <Card class="relative overflow-hidden">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Taxa de Conclusão
                    </CardTitle>
                    <div class="relative size-4">
                        <svg viewBox="0 0 36 36" class="size-4">
                            <circle cx="18" cy="18" r="15.915" fill="none" stroke="currentColor" stroke-width="4" class="text-muted/50" />
                            <circle
                                cx="18"
                                cy="18"
                                r="15.915"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="4"
                                stroke-dasharray="100"
                                :stroke-dashoffset="100 - stats.completionRate"
                                stroke-linecap="round"
                                transform="rotate(-90 18 18)"
                                class="text-emerald-500"
                            />
                        </svg>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-foreground">{{ stats.completionRate }}%</div>
                    <p class="text-xs text-muted-foreground">
                        {{ stats.completed }} de {{ stats.total }} tarefas
                    </p>
                </CardContent>
            </Card>

            <!-- Overdue -->
            <Card class="relative overflow-hidden">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Atrasadas
                    </CardTitle>
                    <AlertTriangle :class="stats.overdue > 0 ? 'size-4 text-red-500' : 'size-4 text-muted-foreground'" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold" :class="stats.overdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-foreground'">
                        {{ stats.overdue }}
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ stats.overdue > 0 ? 'Requer atenção' : 'Nenhuma atrasada' }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Daily Activity Chart -->
            <Card class="lg:col-span-2">
                <CardHeader>
                    <CardTitle class="text-base font-medium">Atividade dos Últimos 7 Dias</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex h-52 items-end gap-2">
                        <div
                            v-for="(label, index) in dailyActivity.labels"
                            :key="label"
                            class="flex flex-1 flex-col items-center gap-1"
                        >
                            <div class="flex w-full flex-col items-center gap-1">
                                <div
                                    class="w-full rounded-t-sm bg-chart-1 transition-all duration-300"
                                    :style="{ height: `${(dailyActivity.created[index] / maxDailyCount) * 100}%`, minHeight: dailyActivity.created[index] > 0 ? '4px' : '0' }"
                                />
                                <div
                                    class="w-full rounded-t-sm bg-chart-2 transition-all duration-300"
                                    :style="{ height: `${(dailyActivity.completed[index] / maxDailyCount) * 100}%`, minHeight: dailyActivity.completed[index] > 0 ? '4px' : '0' }"
                                />
                            </div>
                            <span class="text-xs text-muted-foreground">{{ label }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="size-2.5 rounded-sm bg-chart-1" />
                            <span class="text-muted-foreground">Criadas</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="size-2.5 rounded-sm bg-chart-2" />
                            <span class="text-muted-foreground">Concluídas</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Priority Breakdown -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base font-medium">Por Prioridade</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div
                            v-for="(priority, key) in priorityBreakdown"
                            :key="key"
                            class="space-y-2"
                        >
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div :class="['size-2.5 rounded-full', priorityConfig[key].dot]" />
                                    <span class="text-foreground">{{ priorityConfig[key].label }}</span>
                                </div>
                                <span class="font-medium text-foreground">{{ priority }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-muted">
                                <div
                                    class="h-full rounded-full transition-all duration-500"
                                    :class="priorityConfig[key].dot"
                                    :style="{ width: `${(priority / maxPriorityCount) * 100}%` }"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Tasks Lists Row -->
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <!-- Today's Tasks -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0">
                    <CardTitle class="text-base font-medium">
                        <CalendarDays class="mr-2 inline size-4 text-muted-foreground" />
                        Hoje
                    </CardTitle>
                    <Badge variant="secondary" class="text-xs">
                        {{ todayTasks.length }}
                    </Badge>
                </CardHeader>
                <CardContent>
                    <div v-if="todayTasks.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
                        <CheckCircle2 class="mb-2 size-10 text-muted-foreground/30" />
                        <p class="text-sm text-muted-foreground">Nenhuma tarefa para hoje</p>
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="task in todayTasks"
                            :key="task.id"
                            class="group flex items-start gap-3 rounded-lg p-2 transition-colors hover:bg-accent"
                        >
                            <div :class="['mt-1 size-2 shrink-0 rounded-full', priorityConfig[task.priority].dot]" />
                            <div class="flex-1 space-y-1">
                                <p
                                    class="text-sm font-medium leading-none"
                                    :class="task.is_completed ? 'text-muted-foreground line-through' : 'text-foreground'"
                                >
                                    {{ task.title }}
                                </p>
                                <Badge :class="['text-xs', priorityConfig[task.priority].color]">
                                    {{ task.priority_label }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Upcoming Tasks -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0">
                    <CardTitle class="text-base font-medium">
                        <Clock class="mr-2 inline size-4 text-muted-foreground" />
                        Próximos 7 Dias
                    </CardTitle>
                    <Badge variant="secondary" class="text-xs">
                        {{ upcomingTasks.length }}
                    </Badge>
                </CardHeader>
                <CardContent>
                    <div v-if="upcomingTasks.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
                        <CalendarDays class="mb-2 size-10 text-muted-foreground/30" />
                        <p class="text-sm text-muted-foreground">Nenhuma tarefa próxima</p>
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="task in upcomingTasks"
                            :key="task.id"
                            class="group flex items-start gap-3 rounded-lg p-2 transition-colors hover:bg-accent"
                        >
                            <div :class="['mt-1 size-2 shrink-0 rounded-full', priorityConfig[task.priority].dot]" />
                            <div class="flex-1 space-y-1">
                                <p class="text-sm font-medium leading-none text-foreground">
                                    {{ task.title }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <Badge :class="['text-xs', priorityConfig[task.priority].color]">
                                        {{ task.priority_label }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">
                                        {{ task.due_date ? formatDate(task.due_date) : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Recent Tasks -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0">
                    <CardTitle class="text-base font-medium">
                        <Flag class="mr-2 inline size-4 text-muted-foreground" />
                        Recentes
                    </CardTitle>
                    <Link href="/tasks" class="text-xs text-muted-foreground hover:text-foreground transition-colors">
                        Ver todas
                        <ChevronRight class="ml-0.5 inline size-3" />
                    </Link>
                </CardHeader>
                <CardContent>
                    <div v-if="recentTasks.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
                        <ListTodo class="mb-2 size-10 text-muted-foreground/30" />
                        <p class="text-sm text-muted-foreground">Nenhuma tarefa ainda</p>
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="task in recentTasks"
                            :key="task.id"
                            class="group flex items-start gap-3 rounded-lg p-2 transition-colors hover:bg-accent"
                        >
                            <CheckCircle2
                                v-if="task.is_completed"
                                class="mt-0.5 size-4 shrink-0 text-emerald-500"
                            />
                            <div
                                v-else
                                :class="['mt-0.5 size-4 shrink-0 rounded-full border-2', priorityConfig[task.priority].dot.replace('bg-', 'border-')]"
                            />
                            <div class="flex-1 space-y-1">
                                <p
                                    class="text-sm font-medium leading-none"
                                    :class="task.is_completed ? 'text-muted-foreground line-through' : 'text-foreground'"
                                >
                                    {{ task.title }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <Badge :class="['text-xs', priorityConfig[task.priority].color]">
                                        {{ task.priority_label }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">
                                        {{ formatDate(task.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
