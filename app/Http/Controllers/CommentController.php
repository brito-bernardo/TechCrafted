<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Event $event, Discussion $discussion): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string',
        ]);


        $comment = new Comment([
            'text' => $request->input('content'),
            'user_id' => auth()->id(),
            'discussion_id' => $discussion->id,
            'commented_at' => now(),
        ]);

        $comment->save();


        return redirect()
            ->route('discussion.show', ['event' => $event->id])
            ->with('success', 'Comment added successfully.');
    }

    public function toggleVote(Comment $comment, int $voteType): JsonResponse
    {
        $userId = auth()->id();

        $existingVote = $comment->votes()->where('user_id', $userId)->first();

        if ($existingVote && $existingVote->vote_type === $voteType) {
            $existingVote->delete();
            $newVoteType = 0; // vote removed
        } else {
            $comment->votes()->updateOrCreate(
                ['user_id' => $userId],
                ['vote_type' => $voteType]
            );
            $newVoteType = $voteType;
        }

        $voteCount = $comment->votes()->sum('vote_type');

        return response()->json([
            'success' => true,
            'newVoteType' => $newVoteType,
            'voteCount' => $voteCount,
        ]);
    }


}
