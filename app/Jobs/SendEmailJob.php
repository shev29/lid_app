<?php

namespace App\Jobs;

use App\Models\MasterEmailAccount;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $accountId;
    protected $to;
    protected $cc;
    protected $bcc;
    protected $subject;
    protected $body;
    protected $priority;
    protected $attachments;

    /**
     * Create a new job instance.
     */
    public function __construct($accountId, $to, $cc, $bcc, $subject, $body, $priority = 'normal', $attachments = [])
    {
        $this->accountId = $accountId;
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->subject = $subject;
        $this->body = $body;
        $this->priority = $priority;
        $this->attachments = $attachments;
    }

    /**
     * Execute the job.
     */
    public function handle() {
		Log::info('[Send Email] Started');

        $emailAccount = MasterEmailAccount::findOrFail($this->accountId);

        $transport = new EsmtpTransport($emailAccount->smtp_host, $emailAccount->smtp_port, $emailAccount->smtp_encryption);
        $transport->setUsername($emailAccount->username);
        $transport->setPassword($emailAccount->password);

        $mailer = new Mailer($transport);
        $email = (new Email())
                    ->from(new Address($emailAccount->username, $emailAccount->display_name))
                    ->to($this->to);

        if($this->cc != null) {
            $email = $email->cc($this->cc);
        }

        if($this->bcc != null) {
            $email = $email->bcc($this->bcc);
        }

        $email = $email->subject($this->subject)
                    ->html($this->body)
                    ->priority($this->mapPriority($this->priority));

        $email->getHeaders()->addTextHeader('X-Priority', $this->mapPriority($this->priority));
        $email->getHeaders()->addTextHeader('Importance', $this->mapPriority($this->priority));

        if ($emailAccount->reply_to) {
            $email->replyTo(new Address($emailAccount->reply_to));
        }

        foreach ($this->attachments as $attachment) {
            $email->attachFromPath($attachment['path'], $attachment['name'], $attachment['mimeType']);
        }

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // Handle error (e.g., log the error)
			Log:error('[Send Email] Failed');
        }
		Log::info('[Convert File] Completed');

    }

    protected function mapPriority($priority)
    {
        switch (strtolower($priority)) {
            case 'high':
                return Email::PRIORITY_HIGH;
            case 'low':
                return Email::PRIORITY_LOW;
            default:
                return Email::PRIORITY_NORMAL;
        }
    }
}
