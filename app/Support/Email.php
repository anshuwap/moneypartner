<?php

namespace App\Support;

use Exception as GlobalException;
use Illuminate\Http\Request;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{

    public function composeEmail($data)
    {
        $request = (object)$data;

        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try {

            // Email server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.google.com';            //  smtp host
            $mail->SMTPAuth = false;
            $mail->Username = 'websiteduniya2019@gmail.com';       //  sender username
            $mail->Password = 'Trick@123';             // sender password
            $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
            $mail->Port = 25;                          // port - 587/465

            //$mail->setFrom('websiteduniya2019@gmail.com', 'Nitu Singh');
          	$mail->setFrom('info@moneypartner.in', 'Money Partner');
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

            //$res = $mail->send();

           // json_encode($res);

           if (!$mail->send()) {
            //     // return back()->with("failed", "Email not sent.")->withErrors($mail->ErrorInfo);
               return false;
             } else {
            //     // return back()->with("success", "Email has been sent.");
            return true;
            }
        } catch (Exception $e) {
           return false;

            //return json_encode($e->getMessage());
        }
    }
}
