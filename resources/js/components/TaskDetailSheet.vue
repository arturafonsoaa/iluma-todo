<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    CalendarDays,
    Clock,
    Flag,
    CheckCircle2,
    Circle,
    Trash2,
    X,
    Play,
    Pencil,
    Check,
} from 'lucide-vue-next';
import { computed, nextTick, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetFooter,
    SheetClose,
} from '@/components/ui/sheet';

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

const props = defineProps<{
    task: Task | null;
    open: boolean;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

function isOpen(value: boolean) {
    emit('update:open', value);
}

const editingField = ref<'title' | 'due_date' | 'priority' | null>(null);
const editingValue = ref('');
const titleInput = ref<HTMLInputElement | null>(null);
const dateInput = ref<HTMLInputElement | null>(null);

const priorityConfig = computed(() => {
    const config = {
        low: {
            label: 'Baixa',
            color: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            icon: 'text-green-500',
        },
        medium: {
            label: 'Média',
            color: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            icon: 'text-yellow-500',
        },
        high: {
            label: 'Alta',
            color: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            icon: 'text-orange-500',
        },
        urgent: {
            label: 'Urgente',
            color: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            icon: 'text-red-500',
        },
    };

    return props.task ? config[props.task.priority] : config.medium;
});

const isCompleted = computed(() => {
    return props.task?.completed_at !== null;
});

const statusBadge = computed(() => {
    if (!props.task) {
        return { label: 'Pendente', icon: Circle, color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' };
    }

    if (props.task.completed_at) {
        return { label: 'Concluída', icon: CheckCircle2, color: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' };
    }

    if (props.task.status === 'in_progress') {
        return { label: 'Iniciada', icon: Play, color: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' };
    }

    return { label: 'Pendente', icon: Circle, color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' };
});

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}

function formatDateTime(date: string): string {
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatDateForInput(date: string | null): string {
    if (!date) {
        return '';
    }

    return date.split('T')[0];
}

function getDaysRemaining(): string | null {
    if (!props.task?.due_date) {
        return null;
    }

    const due = new Date(props.task.due_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    due.setHours(0, 0, 0, 0);
    const diff = Math.ceil((due.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));

    if (diff < 0) {
        return `${Math.abs(diff)} dia${Math.abs(diff) !== 1 ? 's' : ''} atrasado`;
    }

    if (diff === 0) {
        return 'Hoje';
    }

    if (diff === 1) {
        return 'Amanhã';
    }

    return `${diff} dias restantes`;
}

const daysRemaining = computed(() => getDaysRemaining());

function toggleComplete() {
    if (!props.task) {
        return;
    }

    router.patch(`/tasks/${props.task.id}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:open', false);
        },
    });
}

function deleteTask() {
    if (!props.task) {
        return;
    }

    router.delete(`/tasks/${props.task.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:open', false);
        },
    });
}

function startEditing(field: 'title' | 'due_date' | 'priority') {
    if (!props.task) {
        return;
    }

    editingField.value = field;

    if (field === 'title') {
        editingValue.value = props.task.title;
        nextTick(() => {
            titleInput.value?.focus();
            titleInput.value?.select();
        });
    } else if (field === 'due_date') {
        editingValue.value = formatDateForInput(props.task.due_date);
        nextTick(() => {
            dateInput.value?.showPicker?.();
        });
    } else if (field === 'priority') {
        editingValue.value = props.task.priority;
    }
}

function cancelEditing() {
    editingField.value = null;
    editingValue.value = '';
}

function saveTitle() {
    if (!props.task || !editingValue.value.trim()) {
        cancelEditing();

        return;
    }

    router.patch(`/tasks/${props.task.id}/title`, {
        title: editingValue.value.trim(),
    }, {
        preserveScroll: true,
        onSuccess: () => {
            cancelEditing();
        },
        onError: () => {
            cancelEditing();
        },
    });
}

function saveDueDate() {
    if (!props.task) {
        cancelEditing();

        return;
    }

    router.patch(`/tasks/${props.task.id}/due-date`, {
        due_date: editingValue.value || null,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            cancelEditing();
        },
        onError: () => {
            cancelEditing();
        },
    });
}

function savePriority() {
    if (!props.task) {
        cancelEditing();

        return;
    }

    router.patch(`/tasks/${props.task.id}/priority`, {
        priority: editingValue.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            cancelEditing();
        },
        onError: () => {
            cancelEditing();
        },
    });
}

function handleKeydown(event: KeyboardEvent, saveFn: () => void) {
    if (event.key === 'Enter') {
        event.preventDefault();
        saveFn();
    } else if (event.key === 'Escape') {
        event.preventDefault();
        cancelEditing();
    }
}
</script>

<template>
    <Sheet :open="open" @update:open="isOpen">
        <SheetContent class="w-full sm:max-w-lg">
            <template v-if="task">
                <SheetHeader class="border-b px-6 py-4 pr-12">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 space-y-2">
                            <div class="group relative">
                                <SheetTitle v-if="editingField !== 'title'" class="text-xl leading-tight cursor-pointer transition-colors hover:text-primary" @click="startEditing('title')">
                                    {{ task.title }}
                                </SheetTitle>
                                <div v-else class="space-y-2">
                                    <input
                                        ref="titleInput"
                                        v-model="editingValue"
                                        type="text"
                                        class="w-full text-xl font-semibold leading-tight bg-transparent border-b-2 border-primary focus:outline-none pb-1"
                                        @keydown.stop="handleKeydown($event, saveTitle)"
                                        @blur="saveTitle"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        Pressione Enter para salvar ou Esc para cancelar
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge
                                    variant="secondary"
                                    :class="statusBadge.color"
                                >
                                    <component
                                        :is="statusBadge.icon"
                                        class="mr-1 size-3"
                                    />
                                    {{ statusBadge.label }}
                                </Badge>
                                <Badge variant="secondary" :class="priorityConfig.color">
                                    <Flag class="mr-1 size-3" :class="priorityConfig.icon" />
                                    {{ priorityConfig.label }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </SheetHeader>

                <div class="flex-1 overflow-y-auto px-6 py-4">
                    <div class="space-y-4">
                        <div class="space-y-4">
                            <div class="group flex items-start gap-3">
                                <CalendarDays class="mt-0.5 size-5 shrink-0 text-muted-foreground" />
                                <div class="flex-1">
                                    <div v-if="editingField !== 'due_date'" class="flex items-center justify-between cursor-pointer -ml-2 pl-2 pr-2 py-1 rounded-md hover:bg-muted/50 transition-colors" @click="startEditing('due_date')">
                                        <div>
                                            <p class="text-sm font-medium">
                                                {{ task.due_date ? formatDate(task.due_date) : 'Sem data' }}
                                            </p>
                                            <p
                                                v-if="daysRemaining"
                                                class="mt-0.5 text-xs"
                                                :class="
                                                    daysRemaining.includes('atrasado')
                                                        ? 'text-red-500'
                                                        : daysRemaining === 'Hoje'
                                                        ? 'text-orange-500'
                                                        : 'text-muted-foreground'
                                                "
                                            >
                                                {{ daysRemaining }}
                                            </p>
                                        </div>
                                        <Pencil class="size-3.5 text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity" />
                                    </div>
                                    <div v-else class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <input
                                                ref="dateInput"
                                                v-model="editingValue"
                                                type="date"
                                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                                @keydown.stop="handleKeydown($event, saveDueDate)"
                                                @blur="saveDueDate"
                                            />
                                            <Button size="sm" variant="ghost" class="size-8 p-0" @click="saveDueDate">
                                                <Check class="size-4" />
                                            </Button>
                                        </div>
                                        <button class="text-xs text-muted-foreground hover:text-foreground transition-colors" @click="editingValue = ''; saveDueDate()">
                                            Remover data
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <Clock class="mt-0.5 size-5 shrink-0 text-muted-foreground" />
                                <div>
                                    <p class="text-sm font-medium">
                                        Criada em {{ formatDateTime(task.created_at) }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="task.started_at" class="flex items-start gap-3">
                                <Play class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                <div>
                                    <p class="text-sm font-medium">
                                        Iniciada em {{ formatDateTime(task.started_at) }}
                                    </p>
                                </div>
                            </div>

                            <div class="group flex items-start gap-3">
                                <Flag class="mt-0.5 size-5 shrink-0" :class="priorityConfig.icon" />
                                <div class="flex-1">
                                    <div v-if="editingField !== 'priority'" class="flex items-center justify-between cursor-pointer -ml-2 pl-2 pr-2 py-1 rounded-md hover:bg-muted/50 transition-colors" @click="startEditing('priority')">
                                        <div>
                                            <p class="text-sm font-medium">Prioridade</p>
                                            <p class="text-xs text-muted-foreground">
                                                {{ priorityConfig.label }}
                                            </p>
                                        </div>
                                        <Pencil class="size-3.5 text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity" />
                                    </div>
                                    <div v-else class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <select
                                                v-model="editingValue"
                                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                                @keydown.stop="handleKeydown($event, savePriority)"
                                                @blur="savePriority"
                                            >
                                                <option value="low">Baixa</option>
                                                <option value="medium">Média</option>
                                                <option value="high">Alta</option>
                                                <option value="urgent">Urgente</option>
                                            </select>
                                            <Button size="sm" variant="ghost" class="size-8 p-0" @click="savePriority">
                                                <Check class="size-4" />
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 size-5 shrink-0 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M20 21a8 8 0 0 0-16 0" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium">Reportada por</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ task.user.name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="isCompleted" class="flex items-center gap-3 rounded-lg bg-green-50 px-4 py-3 dark:bg-green-900/20">
                            <CheckCircle2 class="size-5 text-green-600 dark:text-green-400" />
                            <div>
                                <p class="text-sm font-medium text-green-700 dark:text-green-400">
                                    Tarefa concluída
                                </p>
                                <p class="text-xs text-green-600/80 dark:text-green-500/80">
                                    Concluída em {{ formatDateTime(task.completed_at!) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <SheetFooter class="border-t px-6 py-4">
                    <div class="flex w-full gap-2">
                        <Button
                            variant="outline"
                            class="flex-1"
                            :class="isCompleted ? 'text-orange-600 hover:text-orange-700' : 'text-green-600 hover:text-green-700'"
                            @click="toggleComplete"
                        >
                            <component
                                :is="isCompleted ? Circle : CheckCircle2"
                                class="mr-2 size-4"
                            />
                            {{ isCompleted ? 'Reabrir' : 'Concluir' }}
                        </Button>
                        <Button variant="destructive" class="flex-1" @click="deleteTask">
                            <Trash2 class="mr-2 size-4" />
                            Excluir
                        </Button>
                        <SheetClose as-child>
                            <Button variant="ghost" class="shrink-0">
                                <X class="size-4" />
                            </Button>
                        </SheetClose>
                    </div>
                </SheetFooter>
            </template>
        </SheetContent>
    </Sheet>
</template>
