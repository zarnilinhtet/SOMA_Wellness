<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    // Store a new comment or reply
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|integer',
            'parent_id' => 'nullable|exists:comments,id', // Validates that the parent actually exists
            'content' => 'required|string|max:5000',
        ]);

        // Assuming user is authenticated via Sanctum/Breeze
        Comment::create([
            'class_id' => $validated['class_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return redirect()->back()->with('success', 'Comment posted successfully!');
    }

    public function approve()
    {
        $comments = Comment::with('user', 'classSchedule')->latest()->get();
        return view('backends.comment.comment_index', compact('comments'));
    }

    public function destroyAdminComment($comment)
    {
        $comment = Comment::find($comment);
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully!');

    }

    public function approveComment($comment)
    {
        $comment = Comment::find($comment);
        $comment->update([
            'admin_approval' => true
        ]);

        return redirect()->back()->with('success', 'Comment updated successfully!');
    }
}
