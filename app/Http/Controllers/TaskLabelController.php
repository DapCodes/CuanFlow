<?php

namespace App\Http\Controllers;

use App\Models\TaskLabel;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskLabelController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            'permission:task-labels.manage',
        ];
    }

    /**
     * Display all labels
     */
    public function index()
    {

        $labels = TaskLabel::withCount('tasks')->get();

        return view('tasks.labels.index', compact('labels'));
    }

    /**
     * Store a new label
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:task_labels,name',
            'color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        $label = TaskLabel::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Label berhasil dibuat',
                'label' => $label,
            ]);
        }

        return redirect()->back()->with('success', 'Label berhasil dibuat');
    }

    /**
     * Update label
     */
    public function update(Request $request, TaskLabel $taskLabel)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:task_labels,name,' . $taskLabel->id,
            'color' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        $taskLabel->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Label berhasil diupdate',
                'label' => $taskLabel,
            ]);
        }

        return redirect()->back()->with('success', 'Label berhasil diupdate');
    }

    /**
     * Delete label
     */
    public function destroy(Request $request, TaskLabel $taskLabel)
    {

        $taskLabel->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Label berhasil dihapus',
            ]);
        }

        return redirect()->back()->with('success', 'Label berhasil dihapus');
    }
}
