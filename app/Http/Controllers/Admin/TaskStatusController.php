<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskStatusController extends Controller
{
    public function index()
    {
        $query = TaskStatus::withCount('tasks')->ordered();

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $statuses = $query->get();

        // Stats
        $stats = [
            'total_statuses' => TaskStatus::count(),
            'used_statuses' => TaskStatus::has('tasks')->count(),
            'empty_statuses' => TaskStatus::doesntHave('tasks')->count(),
            'recent' => TaskStatus::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.master.task-statuses.index', compact('statuses', 'stats'));
    }

    public function create()
    {
        return view('admin.master.task-statuses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:50'],
            'order' => ['required', 'integer'],
        ]);

        TaskStatus::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'color' => $validated['color'],
            'order' => $validated['order'],
        ]);

        return redirect()->route('admin.task-statuses.index')
            ->with('success', 'Status tugas berhasil dibuat.');
    }

    public function edit(TaskStatus $taskStatus)
    {
        return view('admin.master.task-statuses.edit', compact('taskStatus'));
    }

    public function update(Request $request, TaskStatus $taskStatus)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:50'],
            'order' => ['required', 'integer'],
        ]);

        $taskStatus->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'color' => $validated['color'],
            'order' => $validated['order'],
        ]);

        return redirect()->route('admin.task-statuses.index')
            ->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function destroy(TaskStatus $taskStatus)
    {
        if ($taskStatus->tasks()->count() > 0) {
            return redirect()->route('admin.task-statuses.index')
                ->with('error', 'Status tidak dapat dihapus karena masih digunakan oleh beberapa tugas.');
        }

        $taskStatus->delete();

        return redirect()->route('admin.task-statuses.index')
            ->with('success', 'Status tugas berhasil dihapus.');
    }
}
