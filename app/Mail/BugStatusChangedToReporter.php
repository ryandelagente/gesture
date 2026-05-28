<?php

namespace App\Mail;

use App\Models\Bug;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BugStatusChangedToReporter extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Bug $bug, public string $oldStatus, public string $newStatus)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Update on your feedback: ' . $this->bug->title)
            ->view('emails.bug-status-changed')
            ->with([
                'bug'       => $this->bug,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
            ]);
    }
}
