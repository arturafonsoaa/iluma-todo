<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    processing?: boolean;
}>();

const emits = defineEmits<{
    submit: [formData: { title: string; due_date: string; priority: string }];
    cancel: [];
}>();
</script>

<template>
    <Form
        action="/tasks"
        method="post"
        reset-on-success
        v-slot="{ errors, processing: formProcessing, data }"
        @success="emits('submit', data as { title: string; due_date: string; priority: string })"
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
            </div>

            <div class="flex justify-end gap-2">
                <Button type="button" variant="outline" @click="emits('cancel')">
                    Cancelar
                </Button>
                <Button type="submit" :disabled="props.processing || formProcessing">
                    <Plus class="size-4" />
                    <span>{{ (props.processing || formProcessing) ? 'Adicionando...' : 'Adicionar Tarefa' }}</span>
                </Button>
            </div>
        </div>
    </Form>
</template>
