# Architecture

This document defines the architecture for this Laravel + Inertia + Vue 3 monolith. It is designed to be **simple, consistent, and AI-friendly**. Every layer has a single, well-defined responsibility.

---

## Principles

1. **Single Responsibility** — Each class does one thing and does it well.
2. **Thin Controllers** — Controllers only handle HTTP concerns: dispatching actions, flash messages, and returning responses.
3. **No Inline Validation** — Validation always lives in a dedicated Form Request.
4. **Explicit Data Flow** — DTOs carry data between layers. No raw arrays cross layer boundaries.
5. **Action-Driven Logic** — All business logic lives in Action classes.
6. **Repository for Complex Queries** — Simple queries stay in the controller; complex or reusable queries move to a Repository.

---

## Directory Structure

```
app/
├── Actions/              # Single-purpose business logic classes
│   └── {Domain}/         # Optional subfolder for large domains
│       └── {Verb}{Entity}Action.php
├── DTOs/                 # Data Transfer Objects
│   └── {Verb}{Entity}DTO.php
├── Http/
│   ├── Controllers/      # Thin controllers (single action per route)
│   │   └── {Entity}Controller.php
│   └── Requests/         # Form Request validation classes
│       └── {Verb}{Entity}Request.php
├── Models/               # Eloquent models
├── Repositories/         # Complex or reusable query classes
│   └── {Entity}Repository.php
├── Concerns/             # Reusable traits
├── Enums/                # PHP enums
├── Providers/            # Service providers
└── Listeners/            # Event listeners
```

---

## Request Flow Diagram

```
HTTP Request
    │
    ▼
┌─────────────────────────┐
│   Form Request          │  Validates input
│   {Verb}{Entity}Request │  Provides toDto() method
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│   DTO                   │  Immutable data carrier
│   {Verb}{Entity}DTO     │  Typed properties, no logic
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│   Action                │  Business logic orchestration
│   {Verb}{Entity}Action  │  Single public method: execute()
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│   Controller            │  HTTP layer only
│   {Entity}Controller    │  Flash messages, returns response
└────────────┬────────────┘
             │
             ▼
       HTTP Response
```

**Note:** For read-only queries with simple, non-reusable logic, the query may stay directly in the controller.

---

## Layers

### 1. Controllers

Controllers are **thin**. They are the HTTP entry point and should only:

- Receive the validated DTO (via Form Request).
- Dispatch an Action.
- Set flash messages (allowed and encouraged).
- Return a response (Redirect, Inertia render, JSON, etc.).

**Rules:**
- One public method per route (single action style).
- No business logic.
- No validation logic.
- May contain simple, non-reusable queries for read operations.
- May dispatch flash messages via `Inertia::flash()`.

```php
// app/Http/Controllers/TaskController.php
namespace App\Http\Controllers;

use App\Actions\StoreTaskAction;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request, StoreTaskAction $action): RedirectResponse
    {
        $action->execute($request->toDto());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Task created successfully!',
        ]);

        return back();
    }
}
```

### 2. Form Requests

Every write operation **must** use a dedicated Form Request for validation.

**Rules:**
- One Form Request per operation.
- Contains only validation rules and authorization logic.
- Must expose a `toDto(): {Specific}DTO` method when the target Action requires a DTO.
- The `toDto()` method may accept additional parameters if needed (e.g., the authenticated user).

```php
// app/Http/Requests/StoreTaskRequest.php
namespace App\Http\Requests;

use App\DTOs\StoreTaskDTO;
use App\Enums\TaskPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'string', Rule::enum(TaskPriority::class)],
            'project_id' => ['nullable', 'exists:projects,id'],
        ];
    }

    public function toDto(): StoreTaskDTO
    {
        return new StoreTaskDTO(
            title: $this->validated('title'),
            description: $this->validated('description'),
            dueDate: $this->validated('due_date'),
            priority: $this->validated('priority')
                ? TaskPriority::from($this->validated('priority'))
                : null,
            projectId: $this->validated('project_id'),
        );
    }
}
```

### 3. DTOs

DTOs are **immutable data carriers**. They transport validated data from the Form Request to the Action.

**Rules:**
- One DTO per operation/flow.
- Named with the pattern `{Verb}{Entity}DTO`.
- Use `readonly` classes with typed, public properties.
- No business logic.
- No Eloquent models inside DTOs (use IDs or primitives).

```php
// app/DTOs/StoreTaskDTO.php
namespace App\DTOs;

use App\Enums\TaskPriority;

readonly class StoreTaskDTO
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?string $dueDate = null,
        public ?TaskPriority $priority = null,
        public ?int $projectId = null,
    ) {}
}
```

### 4. Actions

Actions contain **all business logic**. Each Action handles one specific operation.

**Rules:**
- Named with the pattern `{Verb}{Entity}Action`.
- Single public method: `execute()`.
- Receives a DTO (and/or other explicit parameters like the authenticated user).
- May dispatch other Actions, call Repositories, or interact with Models.
- Returns the result of the operation (model, boolean, void, etc.).

```php
// app/Actions/StoreTaskAction.php
namespace App\Actions;

use App\DTOs\StoreTaskDTO;
use App\Models\Task;
use App\Models\User;

class StoreTaskAction
{
    public function execute(User $user, StoreTaskDTO $dto): Task
    {
        return $user->tasks()->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'due_date' => $dto->dueDate,
            'priority' => $dto->priority,
            'project_id' => $dto->projectId,
        ]);
    }
}
```

### 5. Repositories

Repositories encapsulate **complex or reusable queries**.

**Rules:**
- Named with the pattern `{Entity}Repository`.
- One repository per aggregate root / main entity.
- All methods are **public** and return query results (collections, models, paginators).
- Never contain business logic — only query construction.
- Injected into Actions via constructor or method parameter.

**When to create a Repository:**
- The query has 3+ conditions, joins, or subqueries.
- The same query pattern is used in 2+ places.
- The query involves complex scoping or filtering logic.

**When NOT to create a Repository:**
- Simple `Model::find()`, `Model::where()->first()`, or basic `paginate()`.
- The query is used only once and is trivial.

```php
// app/Repositories/TaskRepository.php
namespace App\Repositories;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class TaskRepository
{
    public function findByUserWithFilters(int $userId, string $filter, ?int $projectId = null): LengthAwarePaginator
    {
        $query = $this->baseQueryForUser($userId, $projectId);

        return $this->applyFilter($query, $filter)->paginate(10);
    }

    public function countByUserWithFilters(int $userId, ?int $projectId = null): array
    {
        $base = $this->baseQueryForUser($userId, $projectId);

        return [
            'pending' => (clone $base)->whereNull('completed_at')->count(),
            'completed' => (clone $base)->whereNotNull('completed_at')->count(),
            'trash' => (clone $base)->onlyTrashed()->count(),
        ];
    }

    private function baseQueryForUser(int $userId, ?int $projectId = null): Builder
    {
        $query = Task::where('user_id', $userId)->with(['user', 'project'])->latest();

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query;
    }

    private function applyFilter(Builder $query, string $filter): Builder
    {
        return match ($filter) {
            'completed' => $query->whereNotNull('completed_at'),
            'trash' => $query->onlyTrashed(),
            default => $query->whereNull('completed_at'),
        };
    }
}
```

---

## Naming Conventions

| Type | Pattern | Example |
|---|---|---|
| Controller | `{Entity}Controller` | `TaskController` |
| Controller method | `{verb}` (lowercase) | `store`, `update`, `destroy`, `restore` |
| Form Request | `{Verb}{Entity}Request` | `StoreTaskRequest`, `UpdateTaskRequest` |
| DTO | `{Verb}{Entity}DTO` | `StoreTaskDTO`, `UpdateTaskDTO` |
| Action | `{Verb}{Entity}Action` | `StoreTaskAction`, `CompleteTaskAction` |
| Repository | `{Entity}Repository` | `TaskRepository`, `ProjectRepository` |

### Verb Guide

| Verb | Usage |
|---|---|
| `Store` | Create a new resource |
| `Update` | Modify an existing resource |
| `Delete` | Remove a resource (soft or hard) |
| `Restore` | Restore a soft-deleted resource |
| `Complete` | Domain-specific action (e.g., mark task as done) |
| `Assign` | Domain-specific action (e.g., assign task to user) |
| `Fetch` | Read/query operation (when using a Repository) |

---

## Namespace Conventions

| Layer | Namespace |
|---|---|
| Controllers | `App\Http\Controllers` |
| Form Requests | `App\Http\Requests` |
| DTOs | `App\DTOs` |
| Actions | `App\Actions` |
| Repositories | `App\Repositories` |
| Models | `App\Models` |
| Enums | `App\Enums` |
| Concerns (traits) | `App\Concerns` |

---

## Complete Example: Update Task

### Form Request

```php
// app/Http/Requests/UpdateTaskRequest.php
namespace App\Http\Requests;

use App\DTOs\UpdateTaskDTO;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');
        return $task instanceof Task && $task->user_id === auth()->id();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function toDto(Task $task): UpdateTaskDTO
    {
        return new UpdateTaskDTO(
            task: $task,
            title: $this->validated('title'),
            description: $this->validated('description'),
            dueDate: $this->validated('due_date'),
        );
    }
}
```

### DTO

```php
// app/DTOs/UpdateTaskDTO.php
namespace App\DTOs;

use App\Models\Task;

readonly class UpdateTaskDTO
{
    public function __construct(
        public Task $task,
        public string $title,
        public ?string $description = null,
        public ?string $dueDate = null,
    ) {}
}
```

### Action

```php
// app/Actions/UpdateTaskAction.php
namespace App\Actions;

use App\DTOs\UpdateTaskDTO;

class UpdateTaskAction
{
    public function execute(UpdateTaskDTO $dto): void
    {
        $dto->task->update([
            'title' => $dto->title,
            'description' => $dto->description,
            'due_date' => $dto->dueDate,
        ]);
    }
}
```

### Controller

```php
// app/Http/Controllers/TaskController.php
namespace App\Http\Controllers;

use App\Actions\UpdateTaskAction;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    public function update(UpdateTaskRequest $request, Task $task, UpdateTaskAction $action): RedirectResponse
    {
        $dto = $request->toDto($task);
        $action->execute($dto);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Task updated successfully!',
        ]);

        return back();
    }
}
```

---

## Decision Tree

```
New feature or endpoint?
│
├── Is it a read operation?
│   ├── Query is simple and used once → Keep in Controller
│   └── Query is complex or reused → Create Repository
│
└── Is it a write/mutation operation?
    ├── Create Form Request for validation
    ├── Create DTO for data transport
    ├── Create Action for business logic
    └── Wire Controller to dispatch Action with DTO
```

---

## Anti-Patterns (Do NOT Do This)

- **Validation in controller** — Always use a Form Request.
- **Business logic in controller** — Move to an Action.
- **Raw arrays crossing layers** — Use DTOs.
- **Multiple operations in one Action** — One Action, one operation.
- **Generic DTOs** — One DTO per flow, even if fields overlap.
- **Model logic in Action** — Keep model-specific behavior on the model (scopes, accessors, casts).
- **Business logic in Repository** — Repositories only build and execute queries.
