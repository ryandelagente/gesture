<?php

namespace App\Mail;

use App\Models\Bug;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BugAssignedToYou extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Bug $bug, public User $assignee)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Bug assigned to you: ' . $this->bug->title)
            ->view('emails.bug-assigned')
            ->with([
                'bug'      => $this->bug,
                'assignee' => $this->assignee,
            ]);
    }
}
