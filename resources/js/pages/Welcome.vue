<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ListTodo, Github, ArrowRight, CheckCircle2 } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const scrolled = ref(false);
const mobileMenuOpen = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 20;
    });
});

function scrollToSection(id: string) {
    const el = document.getElementById(id);

    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }

    mobileMenuOpen.value = false;
}

const roadmap = [
    'Codigo aberto e gratuito',
    'Contribuicoes via pull requests',
    'Decisoes tecnicas publicas',
];
</script>

<template>
    <Head title="Iluma Todo Open Source" />

    <div class="min-h-screen bg-background font-sans text-foreground antialiased">
        <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(60rem_28rem_at_50%_-2%,rgba(245,158,11,0.22),transparent_70%)]" />

        <nav
            class="fixed top-0 right-0 left-0 z-50 transition-all duration-300"
            :class="
                scrolled
                    ? 'border-b border-amber-200/60 bg-background/85 shadow-sm backdrop-blur-xl dark:border-amber-900/40'
                    : ''
            "
        >
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="flex h-14 items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-7 w-7 items-center justify-center rounded-md bg-primary text-primary-foreground">
                            <ListTodo class="h-4 w-4" />
                        </div>
                        <span class="text-base font-semibold tracking-tight">Iluma Todo</span>
                    </div>

                    <div class="hidden items-center gap-3 md:flex">
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="dashboard()"
                                class="rounded-md bg-primary px-3.5 py-1.5 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                            >
                                Entrar
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="rounded-md bg-primary px-3.5 py-1.5 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                Criar conta
                            </Link>
                        </template>
                    </div>

                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="cursor-pointer rounded-md p-1.5 text-muted-foreground md:hidden"
                    >
                        <svg
                            v-if="!mobileMenuOpen"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg
                            v-else
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div v-if="mobileMenuOpen" class="border-t py-3 md:hidden">
                    <div class="flex flex-col gap-1">
                        <button
                            @click="scrollToSection('community')"
                            class="cursor-pointer rounded-md px-3 py-2 text-left text-sm text-muted-foreground hover:bg-accent hover:text-foreground"
                        >
                            Comunidade
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <section class="pb-14 pt-24 md:pb-20 md:pt-32">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="mx-auto max-w-3xl text-center">
                    <h1 class="mt-5 text-4xl leading-tight font-bold tracking-tight sm:text-5xl lg:text-6xl">
                        Organize seu trabalho.
                        <br />
                        <span class="text-primary">Construa em publico.</span>
                    </h1>

                    <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-muted-foreground sm:text-lg">
                        Gestao de tarefas simples e eficaz, construida de forma aberta com contribuicoes da comunidade.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="dashboard()"
                                class="group inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                Ir para dashboard
                                <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="register()"
                                class="group inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                Comecar
                                <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                            </Link>
                            <Link
                                :href="login()"
                                class="inline-flex items-center rounded-md border border-amber-200 px-5 py-2.5 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-foreground dark:border-amber-900/50"
                            >
                                Ja tenho conta
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <section id="community" class="border-t py-16 md:py-24">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Contribua</h2>
                    <p class="mt-3 text-muted-foreground">
                        Projeto aberto para quem quer usar e quem quer contribuir.
                    </p>
                </div>

                <div class="mx-auto mt-10 max-w-lg space-y-3 text-center">
                    <div v-for="step in roadmap" :key="step" class="flex items-center justify-center gap-3">
                        <CheckCircle2 class="h-5 w-5 shrink-0 text-primary" />
                        <span class="text-sm text-muted-foreground">{{ step }}</span>
                    </div>
                </div>

                <div class="mt-10 flex justify-center">
                    <a
                        href="https://github.com/arturafonsoaa/iluma-todo"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group inline-flex items-center gap-2 rounded-md border px-5 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                    >
                        <Github class="h-4 w-4" />
                        Repositorio no GitHub
                        <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                    </a>
                </div>
            </div>
        </section>

        <footer class="border-t py-8">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="flex flex-col items-center justify-between gap-4 text-center sm:flex-row sm:text-left">
                    <div class="flex items-center gap-2">
                        <div class="flex h-6 w-6 items-center justify-center rounded-md bg-primary text-primary-foreground">
                            <ListTodo class="h-3.5 w-3.5" />
                        </div>
                        <span class="text-sm font-medium">Iluma Todo</span>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Codigo aberto. Contribuicoes bem-vindas.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
