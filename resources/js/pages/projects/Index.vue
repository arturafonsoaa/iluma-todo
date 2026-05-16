<script setup lang="ts">
import { Head, Form } from '@inertiajs/vue3';
import { Folder, Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as projectsIndex } from '@/routes/projects';

interface Project {
    id: number;
    name: string;
    created_at: string;
}

defineProps<{
    projects: Project[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Projetos',
                href: projectsIndex(),
            },
        ],
    },
});

const dialogOpen = ref(false);

function handleSubmit() {
    dialogOpen.value = false;
    toast.success('Projeto criado com sucesso!');
}
</script>

<template>
    <Head title="Projetos" />

    <div class="flex h-full flex-1 flex-col gap-8 overflow-x-auto p-4 md:p-8">
        <div class="mx-auto w-full max-w-5xl space-y-8">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <h1 class="text-3xl font-semibold tracking-tight text-foreground">
                        Meus Projetos
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Agrupe suas tarefas em projetos
                    </p>
                </div>

                <Dialog v-model:open="dialogOpen">
                    <DialogTrigger as-child>
                        <Button variant="outline">
                            <Plus class="size-4" />
                            <span>Novo projeto</span>
                        </Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Novo Projeto</DialogTitle>
                            <DialogDescription>
                                Preencha o nome para criar um novo projeto.
                            </DialogDescription>
                        </DialogHeader>
                        <Form action="/projects" method="post" reset-on-success v-slot="{ errors, processing }" @success="handleSubmit">
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <Label for="name">Nome</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        type="text"
                                        placeholder="Nome do projeto"
                                        required
                                    />
                                    <InputError :message="errors.name" />
                                </div>
                                <div class="flex justify-end gap-2">
                                    <Button type="button" variant="outline" @click="dialogOpen = false">
                                        Cancelar
                                    </Button>
                                    <Button type="submit" :disabled="processing">
                                        {{ processing ? 'Criando...' : 'Criar projeto' }}
                                    </Button>
                                </div>
                            </div>
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>

            <div v-if="projects.length === 0" class="py-16 text-center">
                <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-muted">
                    <Folder class="size-8 text-muted-foreground" />
                </div>
                <h3 class="mt-4 text-lg font-medium text-foreground">
                    Nenhum projeto criado
                </h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Crie seu primeiro projeto acima para começar.
                </p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="rounded-xl border bg-card p-6 shadow-sm transition-all hover:shadow-md"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-blue-500/10">
                            <Folder class="size-5 text-blue-600" />
                        </div>
                        <div class="flex-1 truncate">
                            <h3 class="text-base font-semibold text-foreground">
                                {{ project.name }}
                            </h3>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Criado em {{ new Date(project.created_at).toLocaleDateString('pt-BR') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
