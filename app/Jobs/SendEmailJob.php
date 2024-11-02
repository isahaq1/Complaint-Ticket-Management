<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use App\Mail\ComplaintStatusMail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
 
     protected $details;
     protected $header;

     public function __construct($details, $header)
     {
         $this->details = $details;
         $this->header = $header;
     }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mailData = [
            'title' => $this->details['title'],
            'email' => $this->details['email'],
            'subject' => $this->details['subject'],
            'body' => $this->details['body'],
            
        ];
        
        Mail::to($mailData['email'])->send(new ComplaintStatusMail($mailData));
    }
}
