<?php

function custom_mail($address, $subject, $message, $additional_headers = false, $additional_parameters = false) {
    require_once 'PHPMailer/PHPMailerAutoload.php';
    $mail            = new PHPMailer;
    $mail->SMTPDebug = true;
    $mail->CharSet   = 'UTF-8';
    $mail->setLanguage('ru');

    $send_using_gmail = false;

    if ($send_using_gmail) {
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host       = 'smtp.gmail.com';
        $mail->Port       = 465;
        $mail->Username   = 'coder12@web-axioma.ru';
        $mail->Password   = 'rxdyr454';
    }

    $mail->addAddress($address);
    $mail->SetFrom('noreplay@mysiberiawear.ru', 'MySiberia');

    $additional_headers = explode(PHP_EOL, $additional_headers);
    if (count($additional_headers)) {
        foreach ($additional_headers as $header) {
            $ccs  = getCC_BCC($header, "CC:");
            $bccs = getCC_BCC($header, "BCC:");

            if (count($ccs)) {
                foreach ($ccs as $cc) {
                    $mail->addCC(trim($cc));
                }
            }

            if (count($bccs)) {
                foreach ($bccs as $bcc) {
                    $mail->addBCC(trim($bcc));
                }
            }

            /* if (startsWith($header, "Reply-To:")) {
              $replayto = str_replace("Reply-To:", "", $header);
              $mail->AddReplyTo(trim($replayto), 'MySiberia');
              } */
        }
    }

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }

    if (!$mail->send()) {
        echo $mail->ErrorInfo;
    }
    $mail->clearAddresses();
    $mail->ClearCustomHeaders();
}

function getCC_BCC($header, $field) {
    $out = array();
    if (startsWith($header, $field)) {
        if (strpos($header, ",") === false) {
            $out[] = trim(str_replace($field, "", $header));
        } else {
            $arr = explode(",", str_replace($field, "", $header));
            foreach ($arr as $tmp) {
                $out[] = trim($tmp);
            }
        }
    }

    return $out;
}