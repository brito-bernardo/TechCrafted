<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Event;

class DiscussionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('acess.ticket')->only('show');
    }

    public function show($eventId)
    {
        try {
            error_log('opa');

            $event = Event::findOrFail($eventId);
            $user = auth()->user();

            if (!$event->ticket()->whereIn('status', ['PAID', 'READ'])->where('user_id', $user->id)->exists()) {
                return back()->withError('You cannot access this discussion because you do not have valid a ticket.');
            }

            $userHasTicket = $event->ticket->contains('user_id', $user->id);
            $isOrganizer = $event->owner->id == $user->id;

            if (!($userHasTicket || $isOrganizer || $user->isAdmin())) {
                abort(403, 'You do not have access to this discussion.');
            }

            $discussion = $event->discussion;
            $comments = Comment::getCommentsForDiscussion($discussion->id) ?? collect();
            $userVotes = $user->votesForDiscussion($discussion);

            return view('layouts.event.discussion.show', compact('discussion', 'comments', 'event', 'userVotes'));

        } catch (\Exception $e) {
            return back()->withError('Error accessing discussion: ' . $e->getMessage());
        }
    }
}
