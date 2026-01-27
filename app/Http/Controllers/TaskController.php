<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskLabel;
use App\Models\TaskStatus;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStatusChangedNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:tasks.view', only: ['index', 'tableView', 'calendarView', 'getCalendarTasks', 'show', 'getActivities']),
            new Middleware('permission:tasks.create', only: ['store']),
            new Middleware('permission:tasks.update', only: ['update', 'updateStatus']),
            new Middleware('permission:tasks.delete', only: ['destroy']),
            new Middleware('permission:tasks.assign', only: ['assignUsers']),
        ];
    }

    /**
     * Display kanban board view
     */
    public function index(Request $request)
    {

        $statuses = TaskStatus::ordered()->with(['tasks' => function ($query) use ($request) {
            $query->with(['assignees', 'labels', 'creator', 'status']);

            // Apply filters
            if ($request->filled('assignee')) {
                $query->assignedTo($request->assignee);
            }

            if ($request->filled('label')) {
                $query->withLabel($request->label);
            }

            if ($request->filled('priority')) {
                $query->byPriority($request->priority);
            }

            $query->latest();
        }])->get();

        $labels = TaskLabel::all();

        // Get assignable users (only specific roles)
        $assignableUsers = User::role(['kasir', 'supervisor', 'inventaris', 'produksi'])->get();

        // Calculate statistics
        $stats = [
            'total' => Task::count(),
            'pending' => Task::whereHas('status', fn ($q) => $q->where('slug', 'menunggu'))->count(),
            'in_progress' => Task::whereHas('status', fn ($q) => $q->where('slug', 'sedang-berlangsung'))->count(),
            'completed' => Task::whereHas('status', fn ($q) => $q->where('slug', 'selesai'))->count(),
        ];

        return view('tasks.index', compact('statuses', 'labels', 'assignableUsers', 'stats'));
    }

    /**
     * Display table view
     */
    public function tableView(Request $request)
    {

        $query = Task::with(['assignees', 'labels', 'creator', 'status']);

        // Apply filters
        if ($request->filled('assignee')) {
            $query->assignedTo($request->assignee);
        }

        if ($request->filled('label')) {
            $query->withLabel($request->label);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        $tasks = $query->latest()->paginate(20);
        $statuses = TaskStatus::ordered()->get();
        $labels = TaskLabel::all();
        $assignableUsers = User::role(['kasir', 'supervisor', 'inventaris', 'produksi'])->get();

        // Calculate statistics
        $stats = [
            'total' => Task::count(),
            'pending' => Task::whereHas('status', fn ($q) => $q->where('slug', 'menunggu'))->count(),
            'in_progress' => Task::whereHas('status', fn ($q) => $q->where('slug', 'sedang-berlangsung'))->count(),
            'completed' => Task::whereHas('status', fn ($q) => $q->where('slug', 'selesai'))->count(),
        ];

        return view('tasks.table', compact('tasks', 'statuses', 'labels', 'assignableUsers', 'stats'));
    }

    /**
     * Display calendar view
     */
    public function calendarView()
    {

        $statuses = TaskStatus::ordered()->get();
        $labels = TaskLabel::all();
        $assignableUsers = User::role(['kasir', 'supervisor', 'inventaris', 'produksi'])->get();

        // Calculate statistics
        $stats = [
            'total' => Task::count(),
            'pending' => Task::whereHas('status', fn ($q) => $q->where('slug', 'menunggu'))->count(),
            'in_progress' => Task::whereHas('status', fn ($q) => $q->where('slug', 'sedang-berlangsung'))->count(),
            'completed' => Task::whereHas('status', fn ($q) => $q->where('slug', 'selesai'))->count(),
        ];

        return view('tasks.calendar', compact('statuses', 'labels', 'assignableUsers', 'stats'));
    }

    /**
     * Get tasks for calendar (AJAX)
     */
    public function getCalendarTasks(Request $request)
    {

        $tasks = Task::with(['status', 'assignees'])
            ->whereNotNull('deadline')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->deadline->toIso8601String(),
                    'backgroundColor' => $task->status->color,
                    'borderColor' => $task->status->color,
                    'allDay' => false,
                    'extendedProps' => [
                        'status' => $task->status->name,
                        'priority' => $task->priority,
                    ],
                ];
            });

        return response()->json($tasks);
    }

    /**
     * Show task details
     */
    public function show(Task $task)
    {

        $task->load(['assignees', 'labels', 'creator', 'status', 'activities.user']);

        return response()->json($task);
    }

    /**
     * Store a new task
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:task_statuses,id',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'nullable|date',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:task_labels,id',
        ]);

        DB::beginTransaction();
        try {
            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status_id' => $validated['status_id'],
                'priority' => $validated['priority'],
                'deadline' => $validated['deadline'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Attach assignees
            if (! empty($validated['assignees'])) {
                $task->assignees()->attach($validated['assignees']);

                // Log assignment activities
                foreach ($validated['assignees'] as $userId) {
                    $user = User::find($userId);
                    $task->logActivity('assigned', 'assignee', null, $user->name);
                }

                // Send notifications to assignees
                $assignees = User::whereIn('id', $validated['assignees'])->get();
                foreach ($assignees as $assignee) {
                    $assignee->notify(new TaskAssignedNotification($task));
                }
            }

            // Attach labels
            if (! empty($validated['labels'])) {
                $task->labels()->attach($validated['labels']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil dibuat',
                'task' => $task->load(['assignees', 'labels', 'creator', 'status']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat task: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update task
     */
    public function update(Request $request, Task $task)
    {

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'sometimes|required|exists:task_statuses,id',
            'priority' => 'sometimes|required|in:low,medium,high',
            'deadline' => 'nullable|date',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:task_labels,id',
        ]);

        DB::beginTransaction();
        try {
            $oldStatusId = $task->status_id;

            // Update task
            $task->update($validated);

            // Update assignees if provided
            if (isset($validated['assignees'])) {
                $oldAssignees = $task->assignees->pluck('id')->toArray();
                $newAssignees = $validated['assignees'];

                // Find removed and added assignees
                $removed = array_diff($oldAssignees, $newAssignees);
                $added = array_diff($newAssignees, $oldAssignees);

                $task->assignees()->sync($newAssignees);

                // Log and notify removed assignees
                foreach ($removed as $userId) {
                    $user = User::find($userId);
                    $task->logActivity('unassigned', 'assignee', $user->name, null);
                }

                // Log and notify added assignees
                foreach ($added as $userId) {
                    $user = User::find($userId);
                    $task->logActivity('assigned', 'assignee', null, $user->name);
                    $user->notify(new TaskAssignedNotification($task));
                }
            }

            // Update labels if provided
            if (isset($validated['labels'])) {
                $task->labels()->sync($validated['labels']);
            }

            // Send status change notification if status changed
            if (isset($validated['status_id']) && $oldStatusId != $validated['status_id']) {
                foreach ($task->assignees as $assignee) {
                    $assignee->notify(new TaskStatusChangedNotification($task));
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task berhasil diupdate',
                'task' => $task->load(['assignees', 'labels', 'creator', 'status']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate task: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update task status (for drag-drop)
     */
    public function updateStatus(Request $request, Task $task)
    {

        $validated = $request->validate([
            'status_id' => 'required|exists:task_statuses,id',
        ]);

        $oldStatus = $task->status;
        $task->update(['status_id' => $validated['status_id']]);
        $newStatus = $task->status;

        // Send notification
        foreach ($task->assignees as $assignee) {
            $assignee->notify(new TaskStatusChangedNotification($task));
        }

        return response()->json([
            'success' => true,
            'message' => "Status berubah dari '{$oldStatus->name}' ke '{$newStatus->name}'",
        ]);
    }

    /**
     * Assign/unassign users (AJAX)
     */
    public function assignUsers(Request $request, Task $task)
    {

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $oldAssignees = $task->assignees->pluck('id')->toArray();
        $newAssignees = $validated['user_ids'];

        $removed = array_diff($oldAssignees, $newAssignees);
        $added = array_diff($newAssignees, $oldAssignees);

        $task->assignees()->sync($newAssignees);

        // Log and notify
        foreach ($removed as $userId) {
            $user = User::find($userId);
            $task->logActivity('unassigned', 'assignee', $user->name, null);
        }

        foreach ($added as $userId) {
            $user = User::find($userId);
            $task->logActivity('assigned', 'assignee', null, $user->name);
            $user->notify(new TaskAssignedNotification($task));
        }

        return response()->json([
            'success' => true,
            'message' => 'Assignees berhasil diupdate',
        ]);
    }

    /**
     * Get task activities (AJAX)
     */
    public function getActivities(Task $task)
    {

        $activities = $task->activities()->with('user')->get();

        return response()->json($activities);
    }

    /**
     * Delete task
     */
    public function destroy(Task $task)
    {

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dihapus',
        ]);
    }
}
