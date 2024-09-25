<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        return response()->json($user->tasks);
    }

    public function store(Request $request)
    {
         // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Create a new task associated with the authenticated user
        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->user_id = $user->id; // Assuming you have a user_id field in the tasks table
        $task->save();

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function show($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $task->update($request->all());

        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
