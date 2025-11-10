<?php

namespace App\Http\Controllers;

use App\Models\MasterEmailAccount;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function sendEmail($accountId, $to, $subject, $body)
    {
        // Retrieve email account details
        $emailAccount = MasterEmailAccount::findOrFail($accountId);

        // Create transport and mailer
        $transport = new EsmtpTransport($emailAccount->smtp_host, $emailAccount->smtp_port, $emailAccount->smtp_encryption);
        $transport->setUsername($emailAccount->username);
        $transport->setPassword($emailAccount->password);

        $mailer = new Mailer($transport);

        // Create the email
        $email = (new Email())
            ->from(new Address($emailAccount->username, $emailAccount->display_name)) // Set email and display name correctly
            ->to($to)
            ->subject($subject)
            ->html($body);

        try {
            // Send the email
            $mailer->send($email);
            return response()->json(['message' => 'Email sent successfully'], 200);
        }
        catch (TransportExceptionInterface $e) {
            return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
        }
    }
}