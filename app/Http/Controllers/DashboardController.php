<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();

        $totalTasks = $user->tasks()->count();
        $pendingTasks = $user->tasks()->pending()->count();
        $completedTasks = $user->tasks()->completed()->count();
        $overdueTasks = $user->tasks()
            ->pending()
            ->where('due_date', '<', now()->toDateString())
            ->whereNotNull('due_date')
            ->count();

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $priorityBreakdown = $user->tasks()
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        $priorityBreakdown = [
            'low' => (int) ($priorityBreakdown['low'] ?? 0),
            'medium' => (int) ($priorityBreakdown['medium'] ?? 0),
            'high' => (int) ($priorityBreakdown['high'] ?? 0),
            'urgent' => (int) ($priorityBreakdown['urgent'] ?? 0),
        ];

        $dailyCompleted = $user->tasks()
            ->completed()
            ->where('completed_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->date => (int) $row->count])
            ->toArray();

        $last7Days = collect(range(6, 0))->map(fn ($days) => now()->subDays($days)->format('Y-m-d'));
        $dailyCompletedLabels = $last7Days->map(fn ($date) => Carbon::parse($date)->format('D'))->toArray();
        $dailyCompletedData = $last7Days->map(fn ($date) => $dailyCompleted[$date] ?? 0)->toArray();

        $dailyCreated = $user->tasks()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->date => (int) $row->count])
            ->toArray();

        $dailyCreatedData = $last7Days->map(fn ($date) => $dailyCreated[$date] ?? 0)->toArray();

        $todayTasks = $user->tasks()
            ->whereDate('due_date', today())
            ->with('user')
            ->orderByRaw('CASE priority WHEN \'urgent\' THEN 1 WHEN \'high\' THEN 2 WHEN \'medium\' THEN 3 WHEN \'low\' THEN 4 END')
            ->orderBy('created_at', 'desc')
            ->get();

        $upcomingTasks = $user->tasks()
            ->pending()
            ->where('due_date', '>', today())
            ->where('due_date', '<=', now()->addDays(7))
            ->with('user')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $recentTasks = $user->tasks()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        $weeklyStats = $user->tasks()
            ->completed()
            ->where('completed_at', '>=', now()->subDays(6)->startOfDay())
            ->count();

        $previousWeekCompleted = $user->tasks()
            ->completed()
            ->whereBetween('completed_at', [now()->subDays(13)->startOfDay(), now()->subDays(7)->endOfDay()])
            ->count();

        $weeklyTrend = $previousWeekCompleted > 0
            ? round((($weeklyStats - $previousWeekCompleted) / $previousWeekCompleted) * 100)
            : ($weeklyStats > 0 ? 100 : 0);

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $totalTasks,
                'pending' => $pendingTasks,
                'completed' => $completedTasks,
                'overdue' => $overdueTasks,
                'completionRate' => $completionRate,
                'weeklyCompleted' => $weeklyStats,
                'weeklyTrend' => $weeklyTrend,
            ],
            'priorityBreakdown' => $priorityBreakdown,
            'dailyActivity' => [
                'labels' => $dailyCompletedLabels,
                'completed' => $dailyCompletedData,
                'created' => $dailyCreatedData,
            ],
            'todayTasks' => $todayTasks->map(fn ($t) => $this->formatTask($t)),
            'upcomingTasks' => $upcomingTasks->map(fn ($t) => $this->formatTask($t)),
            'recentTasks' => $recentTasks->map(fn ($t) => $this->formatTask($t)),
        ]);
    }

    private function formatTask(Task $task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'priority' => $task->priority->value,
            'priority_label' => $task->priority->label(),
            'due_date' => $task->due_date?->format('Y-m-d'),
            'completed_at' => $task->completed_at?->toISOString(),
            'created_at' => $task->created_at->toISOString(),
            'is_completed' => $task->isCompleted(),
            'user' => [
                'name' => $task->user->name,
            ],
        ];
    }
}
