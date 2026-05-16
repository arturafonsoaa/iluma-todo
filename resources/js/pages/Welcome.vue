<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ListTodo,
    CheckCircle2,
    CircleDot,
    Trash2,
    Flag,
    CalendarDays,
    Target,
    Shield,
    ArrowRight,
} from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
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
const activePlan = ref<'monthly' | 'yearly'>('monthly');

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

const plans = [
    {
        name: 'Starter',
        description: 'Para quem esta comecando',
        monthlyPrice: 'Gratis',
        yearlyPrice: 'Gratis',
        cta: 'Comecar gratis',
        popular: false,
        features: [
            'Ate 20 tarefas',
            'Prioridade basica',
            'Prazos simples',
            'Dashboard basico',
        ],
        notIncluded: [
            'Filtros avancados',
            'Metricas detalhadas',
            'Suporte prioritario',
        ],
    },
    {
        name: 'Pro',
        description: 'Para quem leva produtividade a serio',
        monthlyPrice: 'R$ 19',
        yearlyPrice: 'R$ 15',
        monthlyPeriod: '/mes',
        yearlyPeriod: '/mes',
        yearlyBilled: 'cobrado R$ 180/ano',
        cta: 'Assinar Pro',
        popular: true,
        features: [
            'Tarefas ilimitadas',
            '4 niveis de prioridade',
            'Prazos com alertas',
            'Dashboard completo',
            'Filtros avancados',
            'Metricas detalhadas',
        ],
        notIncluded: ['API access', 'Suporte prioritario'],
    },
    {
        name: 'Team',
        description: 'Para equipes que entregam',
        monthlyPrice: 'R$ 49',
        yearlyPrice: 'R$ 39',
        monthlyPeriod: '/mes',
        yearlyPeriod: '/mes',
        yearlyBilled: 'cobrado R$ 468/ano',
        cta: 'Falar com vendas',
        popular: false,
        features: [
            'Tudo do Pro',
            'Ate 10 membros',
            'Atribuicao de tarefas',
            'Relatorios de equipe',
            'API access',
            'Suporte prioritario',
            'SSO & SAML',
        ],
        notIncluded: [],
    },
];
</script>

<template>
    <Head title="TaskFlow - Organize. Priorize. Entregue."> </Head>

    <div
        class="min-h-screen bg-background font-sans text-foreground antialiased"
    >
        <!-- Navigation -->
        <nav
            class="fixed top-0 right-0 left-0 z-50 transition-all duration-300"
            :class="
                scrolled ? 'border-b bg-background/80 backdrop-blur-xl' : ''
            "
        >
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="flex h-14 items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex h-7 w-7 items-center justify-center rounded-md bg-primary text-primary-foreground"
                        >
                            <ListTodo class="h-4 w-4" />
                        </div>
                        <span class="text-base font-semibold tracking-tight"
                            >TaskFlow</span
                        >
                    </div>

                    <div class="hidden items-center gap-6 md:flex">
                        <button
                            @click="scrollToSection('features')"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            Recursos
                        </button>
                        <button
                            @click="scrollToSection('pricing')"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            Planos
                        </button>
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
                                class="rounded-md bg-amber-500 px-3.5 py-1.5 text-sm font-medium text-black transition-colors hover:bg-amber-400"
                            >
                                Comecar gratis
                            </Link>
                        </template>
                    </div>

                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="rounded-md p-1.5 text-muted-foreground md:hidden"
                    >
                        <svg
                            v-if="!mobileMenuOpen"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div v-if="mobileMenuOpen" class="border-t py-3 md:hidden">
                    <div class="flex flex-col gap-1">
                        <button
                            @click="scrollToSection('features')"
                            class="rounded-md px-3 py-2 text-left text-sm text-muted-foreground hover:bg-accent hover:text-foreground"
                        >
                            Recursos
                        </button>
                        <button
                            @click="scrollToSection('pricing')"
                            class="rounded-md px-3 py-2 text-left text-sm text-muted-foreground hover:bg-accent hover:text-foreground"
                        >
                            Planos
                        </button>
                        <div class="flex flex-col gap-2 border-t pt-3">
                            <template v-if="$page.props.auth.user">
                                <Link
                                    :href="dashboard()"
                                    class="rounded-md bg-primary px-4 py-2 text-center text-sm font-medium text-primary-foreground"
                                >
                                    Dashboard
                                </Link>
                            </template>
                            <template v-else>
                                <Link
                                    :href="login()"
                                    class="rounded-md border px-4 py-2 text-center text-sm text-muted-foreground"
                                >
                                    Entrar
                                </Link>
                                <Link
                                    v-if="canRegister"
                                    :href="register()"
                                    class="rounded-md bg-amber-500 px-4 py-2 text-center text-sm font-medium text-black"
                                >
                                    Comecar gratis
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="pt-24 pb-12 md:pt-32 md:pb-16">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <h1
                        class="text-4xl leading-tight font-bold tracking-tight sm:text-5xl lg:text-6xl"
                    >
                        Organize. Priorize.
                        <br />
                        <span class="text-muted-foreground">Entregue.</span>
                    </h1>

                    <p
                        class="mx-auto mt-4 max-w-lg text-base leading-relaxed text-muted-foreground sm:text-lg"
                    >
                        Gestao de tarefas feita para quem nao tem tempo a
                        perder. Interface limpa, acao rapida, resultados claros.
                    </p>

                    <div
                        class="mt-8 flex flex-wrap items-center justify-center gap-3"
                    >
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="dashboard()"
                                class="group inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                Dashboard
                                <ArrowRight
                                    class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                                />
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="register()"
                                class="group inline-flex items-center gap-2 rounded-md bg-amber-500 px-5 py-2.5 text-sm font-medium text-black transition-colors hover:bg-amber-400"
                            >
                                Comecar gratis
                                <ArrowRight
                                    class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                                />
                            </Link>
                            <Link
                                :href="login()"
                                class="inline-flex items-center rounded-md border px-5 py-2.5 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                            >
                                Ja tenho conta
                            </Link>
                        </template>
                    </div>

                    <div
                        class="mt-5 flex items-center justify-center gap-4 text-xs text-muted-foreground"
                    >
                        <span>Sem cartao</span>
                        <span
                            class="h-1 w-1 rounded-full bg-muted-foreground/40"
                        />
                        <span>Setup em 30s</span>
                        <span
                            class="h-1 w-1 rounded-full bg-muted-foreground/40"
                        />
                        <span>Gratis</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- App Preview — replica exata da pagina de tasks -->
        <section class="pb-12 md:pb-20">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="mx-auto max-w-3xl">
                    <div
                        class="overflow-hidden rounded-xl border bg-card shadow-sm"
                    >
                        <!-- Browser bar -->
                        <div
                            class="flex items-center gap-2 border-b bg-muted/50 px-4 py-2.5"
                        >
                            <div class="flex gap-1.5">
                                <div
                                    class="h-2.5 w-2.5 rounded-full bg-muted-foreground/30"
                                />
                                <div
                                    class="h-2.5 w-2.5 rounded-full bg-muted-foreground/30"
                                />
                                <div
                                    class="h-2.5 w-2.5 rounded-full bg-muted-foreground/30"
                                />
                            </div>
                            <div
                                class="flex-1 rounded-md bg-background px-3 py-1 text-[11px] text-muted-foreground/60"
                            >
                                taskflow.app/tasks
                            </div>
                        </div>

                        <!-- Task page replica -->
                        <div class="p-4 md:p-6">
                            <!-- Header -->
                            <div class="flex items-center justify-between">
                                <div class="space-y-0.5">
                                    <h2
                                        class="text-xl font-semibold tracking-tight"
                                    >
                                        Minhas Tarefas
                                    </h2>
                                    <p class="text-xs text-muted-foreground">
                                        Organize seu dia, uma tarefa de cada vez
                                    </p>
                                </div>
                                <div
                                    class="inline-flex items-center gap-1.5 rounded-md border bg-amber-500 px-3 py-1.5 text-xs font-medium text-black"
                                >
                                    <span class="h-3.5 w-3.5">+</span>
                                    Adicionar
                                </div>
                            </div>

                            <!-- Filter bar -->
                            <div class="mt-4 flex items-center justify-between">
                                <div
                                    class="inline-flex rounded-lg border bg-muted/50 p-0.5"
                                >
                                    <div
                                        class="inline-flex items-center gap-1.5 rounded-md bg-background px-3 py-1.5 text-xs font-medium shadow-sm"
                                    >
                                        <CircleDot class="h-3.5 w-3.5" />
                                        Pendentes
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium text-muted-foreground"
                                    >
                                        <CheckCircle2 class="h-3.5 w-3.5" />
                                        Concluidas
                                    </div>
                                </div>
                                <span class="text-xs text-muted-foreground"
                                    >4 tarefas</span
                                >
                            </div>

                            <!-- Task cards — identical to Index.vue -->
                            <div class="mt-4 space-y-1.5">
                                <div
                                    class="flex items-center gap-3 rounded-lg border bg-card px-4 py-3"
                                >
                                    <div
                                        class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 border-emerald-500 bg-emerald-500 text-white"
                                    >
                                        <svg
                                            class="h-3 w-3"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="3"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M4.5 12.75l6 6 9-13.5"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="flex-1 truncate text-sm font-medium text-muted-foreground line-through"
                                        >Revisar proposta do cliente</span
                                    >
                                    <div
                                        class="flex shrink-0 items-center gap-2"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1 rounded-md border bg-background px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                                        >
                                            <CalendarDays class="h-3 w-3" />
                                            15 mai
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400"
                                        >
                                            <Flag class="h-3 w-3" />
                                            Baixa
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center gap-3 rounded-lg border bg-card px-4 py-3"
                                >
                                    <div
                                        class="h-5 w-5 shrink-0 rounded-full border-2 border-muted-foreground/30"
                                    />
                                    <span
                                        class="flex-1 truncate text-sm font-medium"
                                        >Preparar apresentacao Q4</span
                                    >
                                    <div
                                        class="flex shrink-0 items-center gap-2"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1 rounded-md border bg-background px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                                        >
                                            <CalendarDays class="h-3 w-3" />
                                            18 mai
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-2 py-0.5 text-[10px] font-medium text-red-700 dark:border-red-800 dark:bg-red-950/30 dark:text-red-400"
                                        >
                                            <Flag class="h-3 w-3" />
                                            Urgente
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center gap-3 rounded-lg border bg-card px-4 py-3"
                                >
                                    <div
                                        class="h-5 w-5 shrink-0 rounded-full border-2 border-muted-foreground/30"
                                    />
                                    <span
                                        class="flex-1 truncate text-sm font-medium"
                                        >Atualizar documentacao API</span
                                    >
                                    <div
                                        class="flex shrink-0 items-center gap-2"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1 rounded-md border bg-background px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                                        >
                                            <CalendarDays class="h-3 w-3" />
                                            20 mai
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-700 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-400"
                                        >
                                            <Flag class="h-3 w-3" />
                                            Media
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center gap-3 rounded-lg border bg-card px-4 py-3"
                                >
                                    <div
                                        class="h-5 w-5 shrink-0 rounded-full border-2 border-muted-foreground/30"
                                    />
                                    <span
                                        class="flex-1 truncate text-sm font-medium text-muted-foreground"
                                        >Reuniao de planning</span
                                    >
                                    <div
                                        class="flex shrink-0 items-center gap-2"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full border border-muted bg-muted/50 px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                                        >
                                            <Flag class="h-3 w-3" />
                                            Baixa
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features — Bento Grid com estilo do app -->
        <section id="features" class="border-t py-12 md:py-16">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="mb-10">
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        Feito para quem
                        <span class="text-muted-foreground">executa.</span>
                    </h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Os mesmos recursos que voce ve no app.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <!-- Feature 1 — Wide: Prioridades (replica os badges do app) -->
                    <div
                        class="group col-span-1 rounded-xl border bg-card p-5 shadow-sm transition-shadow hover:shadow-md md:col-span-2"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-500/10"
                            >
                                <Flag
                                    class="h-4 w-4 text-red-600 dark:text-red-400"
                                />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold">
                                    4 niveis de prioridade
                                </h3>
                                <p
                                    class="mt-1 text-sm leading-relaxed text-muted-foreground"
                                >
                                    Do trivial ao critico, cada tarefa tem seu
                                    lugar. Os mesmos badges que voce usa no
                                    dashboard.
                                </p>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 dark:border-red-800 dark:bg-red-950/30 dark:text-red-400"
                                    >
                                        <Flag class="h-3 w-3" />
                                        Urgente
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full border border-orange-200 bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-700 dark:border-orange-800 dark:bg-orange-950/30 dark:text-orange-400"
                                    >
                                        <Flag class="h-3 w-3" />
                                        Alta
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-400"
                                    >
                                        <Flag class="h-3 w-3" />
                                        Media
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400"
                                    >
                                        <Flag class="h-3 w-3" />
                                        Baixa
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2: Prazos -->
                    <div
                        class="group rounded-xl border bg-card p-5 shadow-sm transition-shadow hover:shadow-md"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-500/10"
                            >
                                <CalendarDays
                                    class="h-4 w-4 text-amber-600 dark:text-amber-400"
                                />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold">
                                    Prazos visuais
                                </h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Contagem regressiva com cores. Atrasado =
                                    vermelho. Hoje = laranja.
                                </p>
                                <div class="mt-4 space-y-2">
                                    <div
                                        class="flex items-center justify-between rounded-md bg-red-50 px-3 py-1.5 text-xs dark:bg-red-950/20"
                                    >
                                        <span
                                            class="text-red-600 dark:text-red-400"
                                            >3 dias atrasado</span
                                        >
                                    </div>
                                    <div
                                        class="flex items-center justify-between rounded-md bg-amber-50 px-3 py-1.5 text-xs dark:bg-amber-950/20"
                                    >
                                        <span
                                            class="text-amber-600 dark:text-amber-400"
                                            >Hoje</span
                                        >
                                    </div>
                                    <div
                                        class="flex items-center justify-between rounded-md border bg-background px-3 py-1.5 text-xs"
                                    >
                                        <span class="text-muted-foreground"
                                            >5 dias restantes</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 3: Dashboard -->
                    <div
                        class="group rounded-xl border bg-card p-5 shadow-sm transition-shadow hover:shadow-md"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-500/10"
                            >
                                <Target
                                    class="h-4 w-4 text-violet-600 dark:text-violet-400"
                                />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold">
                                    Dashboard completo
                                </h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Stats, grafico semanal, breakdown por
                                    prioridade.
                                </p>
                                <div class="mt-4 flex items-end gap-1">
                                    <div
                                        class="w-3 rounded-t bg-chart-1/60"
                                        style="height: 16px"
                                    />
                                    <div
                                        class="w-3 rounded-t bg-chart-1/70"
                                        style="height: 28px"
                                    />
                                    <div
                                        class="w-3 rounded-t bg-chart-1/80"
                                        style="height: 20px"
                                    />
                                    <div
                                        class="w-3 rounded-t bg-chart-1"
                                        style="height: 36px"
                                    />
                                    <div
                                        class="w-3 rounded-t bg-chart-2/60"
                                        style="height: 12px"
                                    />
                                    <div
                                        class="w-3 rounded-t bg-chart-2/70"
                                        style="height: 24px"
                                    />
                                    <div
                                        class="w-3 rounded-t bg-chart-2"
                                        style="height: 32px"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 4: Filtros -->
                    <div
                        class="group rounded-xl border bg-card p-5 shadow-sm transition-shadow hover:shadow-md"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-500/10"
                            >
                                <ListTodo
                                    class="h-4 w-4 text-blue-600 dark:text-blue-400"
                                />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold">
                                    Filtros instantaneos
                                </h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Pendentes, Concluidas, Lixeira. Sem reload.
                                </p>
                                <div
                                    class="mt-4 inline-flex rounded-lg border bg-muted/50 p-0.5"
                                >
                                    <div
                                        class="inline-flex items-center gap-1 rounded-md bg-background px-2.5 py-1 text-[11px] font-medium shadow-sm"
                                    >
                                        <CircleDot class="h-3 w-3" />
                                        Pendentes
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-[11px] text-muted-foreground"
                                    >
                                        <CheckCircle2 class="h-3 w-3" />
                                        Concluidas
                                    </div>
                                    <div
                                        class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-[11px] text-muted-foreground"
                                    >
                                        <Trash2 class="h-3 w-3" />
                                        Lixeira
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 5 — Wide: Seguranca -->
                    <div
                        class="group col-span-1 rounded-xl border bg-card p-5 shadow-sm transition-shadow hover:shadow-md md:col-span-2"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10"
                            >
                                <Shield
                                    class="h-4 w-4 text-emerald-600 dark:text-emerald-400"
                                />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold">
                                    Isolamento total
                                </h3>
                                <p
                                    class="mt-1 text-sm leading-relaxed text-muted-foreground"
                                >
                                    Cada usuario ve apenas suas tarefas.
                                    Autenticacao via Laravel Fortify com 2FA
                                    opcional. Do banco ao frontend, seus dados
                                    sao so seus.
                                </p>
                                <div
                                    class="mt-3 flex flex-wrap gap-3 text-xs text-muted-foreground"
                                >
                                    <span class="flex items-center gap-1.5">
                                        <CheckCircle2
                                            class="h-3.5 w-3.5 text-emerald-500"
                                        />
                                        Laravel Fortify
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <CheckCircle2
                                            class="h-3.5 w-3.5 text-emerald-500"
                                        />
                                        2FA / TOTP
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <CheckCircle2
                                            class="h-3.5 w-3.5 text-emerald-500"
                                        />
                                        Ownership checks
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="border-t border-b bg-muted/30 py-8">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold">10K+</div>
                        <div class="mt-0.5 text-xs text-muted-foreground">
                            Usuarios ativos
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">2M+</div>
                        <div class="mt-0.5 text-xs text-muted-foreground">
                            Tarefas concluidas
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">99.9%</div>
                        <div class="mt-0.5 text-xs text-muted-foreground">
                            Uptime
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">4.9</div>
                        <div class="mt-0.5 text-xs text-muted-foreground">
                            Avaliacao media
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section id="pricing" class="py-12 md:py-16">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="mx-auto max-w-xl text-center">
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        Escolha seu ritmo
                    </h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Comece gratis. Escale quando quiser.
                    </p>

                    <div
                        class="mt-6 inline-flex items-center gap-2 rounded-lg border bg-muted/50 p-0.5"
                    >
                        <button
                            @click="activePlan = 'monthly'"
                            class="rounded-md px-4 py-1.5 text-sm font-medium transition-all"
                            :class="
                                activePlan === 'monthly'
                                    ? 'bg-background text-foreground shadow-sm'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                        >
                            Mensal
                        </button>
                        <button
                            @click="activePlan = 'yearly'"
                            class="rounded-md px-4 py-1.5 text-sm font-medium transition-all"
                            :class="
                                activePlan === 'yearly'
                                    ? 'bg-background text-foreground shadow-sm'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                        >
                            Anual
                            <span class="ml-1 text-[10px] text-emerald-600"
                                >-20%</span
                            >
                        </button>
                    </div>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <div
                        v-for="(plan, index) in plans"
                        :key="index"
                        class="flex flex-col rounded-xl border bg-card p-6 shadow-sm"
                        :class="
                            plan.popular
                                ? 'border-amber-500 ring-1 ring-amber-500/20'
                                : ''
                        "
                    >
                        <div v-if="plan.popular" class="-mt-8 mb-2">
                            <span
                                class="rounded-full bg-amber-500 px-2.5 py-0.5 text-[10px] font-bold tracking-wider text-black uppercase"
                                >Popular</span
                            >
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold">
                                {{ plan.name }}
                            </h3>
                            <p class="mt-0.5 text-xs text-muted-foreground">
                                {{ plan.description }}
                            </p>
                        </div>

                        <div class="mt-4">
                            <span class="text-3xl font-bold">
                                {{
                                    activePlan === 'monthly'
                                        ? plan.monthlyPrice
                                        : plan.yearlyPrice
                                }}
                            </span>
                            <span
                                v-if="plan.monthlyPeriod"
                                class="text-sm text-muted-foreground"
                            >
                                {{
                                    activePlan === 'monthly'
                                        ? plan.monthlyPeriod
                                        : plan.yearlyPeriod
                                }}
                            </span>
                            <div
                                v-if="
                                    plan.yearlyBilled && activePlan === 'yearly'
                                "
                                class="mt-0.5 text-[11px] text-muted-foreground"
                            >
                                {{ plan.yearlyBilled }}
                            </div>
                        </div>

                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="mt-5 block rounded-md px-4 py-2 text-center text-sm font-medium transition-colors"
                            :class="
                                plan.popular
                                    ? 'bg-amber-500 text-black hover:bg-amber-400'
                                    : 'border bg-background hover:bg-accent'
                            "
                        >
                            {{ plan.cta }}
                        </Link>
                        <template v-else>
                            <Link
                                :href="register()"
                                class="mt-5 block rounded-md px-4 py-2 text-center text-sm font-medium transition-colors"
                                :class="
                                    plan.popular
                                        ? 'bg-amber-500 text-black hover:bg-amber-400'
                                        : 'border bg-background hover:bg-accent'
                                "
                            >
                                {{ plan.cta }}
                            </Link>
                        </template>

                        <div class="mt-5 space-y-2 border-t pt-4">
                            <div
                                v-for="(feature, fIndex) in plan.features"
                                :key="fIndex"
                                class="flex items-center gap-2 text-sm"
                            >
                                <CheckCircle2
                                    class="h-4 w-4 shrink-0 text-emerald-500"
                                />
                                <span class="text-muted-foreground">{{
                                    feature
                                }}</span>
                            </div>
                            <div
                                v-for="(feature, fIndex) in plan.notIncluded"
                                :key="`not-${fIndex}`"
                                class="flex items-center gap-2 text-sm"
                            >
                                <svg
                                    class="h-4 w-4 shrink-0 text-muted-foreground/40"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                                <span class="text-muted-foreground/50">{{
                                    feature
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="border-t py-12 md:py-16">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        Quem usa, nao volta
                    </h2>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-xl border bg-card p-5 shadow-sm">
                        <div class="flex gap-0.5">
                            <svg
                                v-for="n in 5"
                                :key="n"
                                class="h-3.5 w-3.5 text-amber-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                />
                            </svg>
                        </div>
                        <p
                            class="mt-3 text-sm leading-relaxed text-muted-foreground"
                        >
                            "TaskFlow transformou minha forma de gerenciar
                            projetos. Interface intuitiva e prazos inteligentes
                            sao um divisor de aguas."
                        </p>
                        <div
                            class="mt-4 flex items-center gap-2.5 border-t pt-3"
                        >
                            <div
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary"
                            >
                                AS
                            </div>
                            <div>
                                <div class="text-xs font-medium">Ana Silva</div>
                                <div class="text-[11px] text-muted-foreground">
                                    Product Manager
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-5 shadow-sm">
                        <div class="flex gap-0.5">
                            <svg
                                v-for="n in 5"
                                :key="n"
                                class="h-3.5 w-3.5 text-amber-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                />
                            </svg>
                        </div>
                        <p
                            class="mt-3 text-sm leading-relaxed text-muted-foreground"
                        >
                            "Rapida, bonita e funcional. Uso todos os dias e
                            minha produtividade aumentou significativamente."
                        </p>
                        <div
                            class="mt-4 flex items-center gap-2.5 border-t pt-3"
                        >
                            <div
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary"
                            >
                                CM
                            </div>
                            <div>
                                <div class="text-xs font-medium">
                                    Carlos Mendes
                                </div>
                                <div class="text-[11px] text-muted-foreground">
                                    Desenvolvedor Full Stack
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-5 shadow-sm">
                        <div class="flex gap-0.5">
                            <svg
                                v-for="n in 5"
                                :key="n"
                                class="h-3.5 w-3.5 text-amber-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                />
                            </svg>
                        </div>
                        <p
                            class="mt-3 text-sm leading-relaxed text-muted-foreground"
                        >
                            "Cada detalhe foi pensado para tornar a gestao de
                            tarefas algo prazeroso, nao uma obrigacao."
                        </p>
                        <div
                            class="mt-4 flex items-center gap-2.5 border-t pt-3"
                        >
                            <div
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary"
                            >
                                MC
                            </div>
                            <div>
                                <div class="text-xs font-medium">
                                    Mariana Costa
                                </div>
                                <div class="text-[11px] text-muted-foreground">
                                    Designer UX/UI
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="border-t py-12 md:py-16">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div
                    class="rounded-xl border bg-muted/30 px-6 py-10 text-center md:px-12 md:py-14"
                >
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        Pronto para organizar sua vida?
                    </h2>
                    <p
                        class="mx-auto mt-2 max-w-md text-sm text-muted-foreground"
                    >
                        Junte-se a milhares de usuarios que ja conquistaram seus
                        objetivos.
                    </p>

                    <div
                        class="mt-6 flex flex-col items-center justify-center gap-3 sm:flex-row"
                    >
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="dashboard()"
                                class="group inline-flex items-center gap-2 rounded-md bg-primary px-6 py-2.5 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                Dashboard
                                <ArrowRight
                                    class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                                />
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="register()"
                                class="group inline-flex items-center gap-2 rounded-md bg-amber-500 px-6 py-2.5 text-sm font-medium text-black transition-colors hover:bg-amber-400"
                            >
                                Criar conta gratis
                                <ArrowRight
                                    class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                                />
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t py-8">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div
                    class="flex flex-col items-center justify-between gap-4 sm:flex-row"
                >
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-6 w-6 items-center justify-center rounded-md bg-primary text-primary-foreground"
                        >
                            <ListTodo class="h-3.5 w-3.5" />
                        </div>
                        <span class="text-sm font-semibold">TaskFlow</span>
                    </div>

                    <div
                        class="flex items-center gap-5 text-xs text-muted-foreground"
                    >
                        <button
                            @click="scrollToSection('features')"
                            class="transition-colors hover:text-foreground"
                        >
                            Recursos
                        </button>
                        <button
                            @click="scrollToSection('pricing')"
                            class="transition-colors hover:text-foreground"
                        >
                            Planos
                        </button>
                    </div>

                    <div class="text-xs text-muted-foreground/60">
                        &copy; {{ new Date().getFullYear() }} TaskFlow
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
