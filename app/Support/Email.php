<?php

namespace App\Support;

use Exception as GlobalException;
use Illuminate\Http\Request;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{

    // ========== [ Compose Email ] ================
    public function composeEmail($data)
    {
        $request = (object)$data;

        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try {

        // Email server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'mail.moneypartner.in';            //  smtp host
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@moneypartner.in';       //  sender username
        $mail->Password = 'India@2022';             // sender password
        $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
        $mail->Port = 587;                          // port - 587/465

        $mail->setFrom('noreply@moneypartner.in', 'Moneypartner');
        $mail->addAddress($request->email);
        // $mail->addCC($request->emailCc);
        // $mail->addBCC($request->emailBcc);

        //$mail->addReplyTo('sender@example.com', 'SenderReplyName');

        if (isset($_FILES['emailAttachments'])) {
            for ($i = 0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
                $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
            }
        }

        $mail->isHTML(true);                // Set email content format to HTML

        $mail->Subject = $request->subject;
        $mail->Body    = $request->msg;

        // $mail->AltBody = plain text version of email body;

        if (!$mail->send()) {
            // return back()->with("failed", "Email not sent.")->withErrors($mail->ErrorInfo);
            return false;
        } else {
            // return back()->with("success", "Email has been sent.");
            return true;
            // echo "working";
            // die;
        }

        } catch (GlobalException $e) {
            print_r($e->getMessage());
        }
    }
}
