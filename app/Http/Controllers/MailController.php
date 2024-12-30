<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class MailController extends Controller
{

public function sendTestEmail()
{
    $details = [
        'title' => 'Test Email from Laravel',
        'body' => 'This is a test email to verify the Gmail SMTP configuration.'
    ];

    Mail::to('recipient-email@example.com')->send(new TestMail($details));

    return 'Email Sent!';
}

}
