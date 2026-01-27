<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskLabel;
use Illuminate\Http\Request;

class TaskLabelController extends Controller
{
    public function index()
    {
        $labels = TaskLabel::withCount('tasks')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.master.task-labels.index', compact('labels'));
    }

    public function create()
    {
        return view('admin.master.task-labels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:50'],
        ]);

        TaskLabel::create($validated);

        return redirect()->route('admin.task-labels.index')
            ->with('success', 'Label tugas berhasil dibuat.');
    }

    public function edit(TaskLabel $taskLabel)
    {
        return view('admin.master.task-labels.edit', compact('taskLabel'));
    }

    public function update(Request $request, TaskLabel $taskLabel)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:50'],
        ]);

        $taskLabel->update($validated);

        return redirect()->route('admin.task-labels.index')
            ->with('success', 'Label tugas berhasil diperbarui.');
    }

    public function destroy(TaskLabel $taskLabel)
    {
        if ($taskLabel->tasks()->count() > 0) {
            return redirect()->route('admin.task-labels.index')
                ->with('error', 'Label tidak dapat dihapus karena masih digunakan oleh beberapa tugas.');
        }

        $taskLabel->delete();

        return redirect()->route('admin.task-labels.index')
            ->with('success', 'Label tugas berhasil dihapus.');
    }
}
