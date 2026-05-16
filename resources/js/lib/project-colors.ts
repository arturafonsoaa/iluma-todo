export type ProjectColorKey =
    | 'blue'
    | 'emerald'
    | 'amber'
    | 'orange'
    | 'red'
    | 'violet'
    | 'sky'
    | 'pink';

export interface ProjectColorStyles {
    label: string;
    dot: string;
    trigger: string;
    itemChecked: string;
    icon: string;
    badge: string;
    count: string;
}

export const DEFAULT_PROJECT_COLOR: ProjectColorKey = 'blue';

export const PROJECT_COLORS: Record<ProjectColorKey, ProjectColorStyles> = {
    blue: {
        label: 'Azul',
        dot: 'bg-blue-500',
        trigger:
            'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/30 dark:text-blue-400 dark:hover:bg-blue-900/50 focus-visible:ring-blue-500/20 data-[state=open]:bg-blue-100 dark:data-[state=open]:bg-blue-900/50',
        itemChecked: 'data-[state=checked]:bg-blue-50 dark:data-[state=checked]:bg-blue-950/40',
        icon: 'text-blue-700 dark:text-blue-400',
        badge: 'bg-blue-100/60 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold',
        count: 'text-blue-700 dark:text-blue-400',
    },
    emerald: {
        label: 'Verde',
        dot: 'bg-emerald-500',
        trigger:
            'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400 dark:hover:bg-emerald-900/50 focus-visible:ring-emerald-500/20 data-[state=open]:bg-emerald-100 dark:data-[state=open]:bg-emerald-900/50',
        itemChecked: 'data-[state=checked]:bg-emerald-50 dark:data-[state=checked]:bg-emerald-950/40',
        icon: 'text-emerald-700 dark:text-emerald-400',
        badge: 'bg-emerald-100/60 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-semibold',
        count: 'text-emerald-700 dark:text-emerald-400',
    },
    amber: {
        label: 'Âmbar',
        dot: 'bg-amber-500',
        trigger:
            'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-400 dark:hover:bg-amber-900/50 focus-visible:ring-amber-500/20 data-[state=open]:bg-amber-100 dark:data-[state=open]:bg-amber-900/50',
        itemChecked: 'data-[state=checked]:bg-amber-50 dark:data-[state=checked]:bg-amber-950/40',
        icon: 'text-amber-700 dark:text-amber-400',
        badge: 'bg-amber-100/60 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-semibold',
        count: 'text-amber-700 dark:text-amber-400',
    },
    orange: {
        label: 'Laranja',
        dot: 'bg-orange-500',
        trigger:
            'border-orange-200 bg-orange-50 text-orange-700 hover:bg-orange-100 dark:border-orange-800 dark:bg-orange-950/30 dark:text-orange-400 dark:hover:bg-orange-900/50 focus-visible:ring-orange-500/20 data-[state=open]:bg-orange-100 dark:data-[state=open]:bg-orange-900/50',
        itemChecked: 'data-[state=checked]:bg-orange-50 dark:data-[state=checked]:bg-orange-950/40',
        icon: 'text-orange-700 dark:text-orange-400',
        badge: 'bg-orange-100/60 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 font-semibold',
        count: 'text-orange-700 dark:text-orange-400',
    },
    red: {
        label: 'Vermelho',
        dot: 'bg-red-500',
        trigger:
            'border-red-200 bg-red-50 text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-950/30 dark:text-red-400 dark:hover:bg-red-900/50 focus-visible:ring-red-500/20 data-[state=open]:bg-red-100 dark:data-[state=open]:bg-red-900/50',
        itemChecked: 'data-[state=checked]:bg-red-50 dark:data-[state=checked]:bg-red-950/40',
        icon: 'text-red-700 dark:text-red-400',
        badge: 'bg-red-100/60 dark:bg-red-900/30 text-red-700 dark:text-red-400 font-semibold',
        count: 'text-red-700 dark:text-red-400',
    },
    violet: {
        label: 'Violeta',
        dot: 'bg-violet-500',
        trigger:
            'border-violet-200 bg-violet-50 text-violet-700 hover:bg-violet-100 dark:border-violet-800 dark:bg-violet-950/30 dark:text-violet-400 dark:hover:bg-violet-900/50 focus-visible:ring-violet-500/20 data-[state=open]:bg-violet-100 dark:data-[state=open]:bg-violet-900/50',
        itemChecked: 'data-[state=checked]:bg-violet-50 dark:data-[state=checked]:bg-violet-950/40',
        icon: 'text-violet-700 dark:text-violet-400',
        badge: 'bg-violet-100/60 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 font-semibold',
        count: 'text-violet-700 dark:text-violet-400',
    },
    sky: {
        label: 'Céu',
        dot: 'bg-sky-500',
        trigger:
            'border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100 dark:border-sky-800 dark:bg-sky-950/30 dark:text-sky-400 dark:hover:bg-sky-900/50 focus-visible:ring-sky-500/20 data-[state=open]:bg-sky-100 dark:data-[state=open]:bg-sky-900/50',
        itemChecked: 'data-[state=checked]:bg-sky-50 dark:data-[state=checked]:bg-sky-950/40',
        icon: 'text-sky-700 dark:text-sky-400',
        badge: 'bg-sky-100/60 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 font-semibold',
        count: 'text-sky-700 dark:text-sky-400',
    },
    pink: {
        label: 'Rosa',
        dot: 'bg-pink-500',
        trigger:
            'border-pink-200 bg-pink-50 text-pink-700 hover:bg-pink-100 dark:border-pink-800 dark:bg-pink-950/30 dark:text-pink-400 dark:hover:bg-pink-900/50 focus-visible:ring-pink-500/20 data-[state=open]:bg-pink-100 dark:data-[state=open]:bg-pink-900/50',
        itemChecked: 'data-[state=checked]:bg-pink-50 dark:data-[state=checked]:bg-pink-950/40',
        icon: 'text-pink-700 dark:text-pink-400',
        badge: 'bg-pink-100/60 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400 font-semibold',
        count: 'text-pink-700 dark:text-pink-400',
    },
};

export const ALL_PROJECTS_STYLES = {
    trigger:
        'border-border bg-background text-foreground hover:bg-muted/50 dark:hover:bg-muted/30 focus-visible:ring-ring/20 data-[state=open]:bg-muted/50',
    itemChecked: 'data-[state=checked]:bg-muted/50',
    icon: 'text-muted-foreground',
    badge: 'bg-muted text-muted-foreground font-semibold',
    count: 'text-muted-foreground',
};

export const PROJECT_COLOR_OPTIONS = Object.entries(PROJECT_COLORS).map(([key, styles]) => ({
    key: key as ProjectColorKey,
    ...styles,
}));

export function getProjectColorStyles(color?: string | null): ProjectColorStyles {
    if (color && color in PROJECT_COLORS) {
        return PROJECT_COLORS[color as ProjectColorKey];
    }

    return PROJECT_COLORS[DEFAULT_PROJECT_COLOR];
}
