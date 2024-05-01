<?php
use PHPMailer\PHPMailer\PHPMailer;

require $_SERVER['DOCUMENT_ROOT'].'/server/PHPMailer/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/server/PHPMailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/server/PHPMailer/SMTP.php';

class Email {
  private $toEmail;
  private $toName;
  private $type;
  private $customData;

  function __construct($toEmail, $toName, $type, $customData) {
    $this->toEmail = $toEmail;
    $this->toName = $toName;
    $this->type = $type;
    $this->customData = $customData;
  }

  function sendMail() {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth   = true;
    
    // TODO: verificar cert. ssl
    $mail->SMTPOptions = array(
      'ssl' => [
          'verify_peer' => false,
      ],
    );
    $mail->SMTPSecure = 'tls';
    $mail->Host       = 'smtp.gmail.com';
    $mail->Port       = 587;
    $mail->Username   = 'bibliopocketinfo@gmail.com';
    $mail->Password   = 'qeum vkue udvk urje ';
    $mail->Port       = 25;
    $mail->CharSet    = 'UTF-8';
    
    
    $mail->SetFrom('bibliopocketinfo@gmail.com', 'BiblioPocket');
    $mail->AddAddress($this->toEmail, $this->toName);
    $mail->Subject = $this->getSubjectMail();
    
    $mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT'].'/client/assets/images/mails/heading_mail.png', 'heading_email');
    // Template by: Envato Tuts [https://codepen.io/tutsplus/pen/aboBgLX]
    $mail->MsgHTML($this->getBodyMail());

    if (!$mail->Send()) {
      echo "
        <dialog class='modal' id='error-mail' open>
          <h3>Parece que hubo un problema en el envío del correo...</h3>
          <p>Comprueba que has introducido bien tu correo y, si vuelve a ocurrir, vuelve a intentarlo más tarde.</p>
        </dialog>
      ";
      return false;
    }

    return true;
  }

  private function getSubjectMail() {
    $subjectMail = "";

    switch($this->type) {
      // Registro de nueva cuenta (código de confirmación)
      case 0:
        $subjectMail = "Código de confirmación";
        break;
      // Recuperación de la cuenta (contraseña olvidada)
      case 1:
        $subjectMail = "Recuperación de cuenta";
        break;
    }

    return $subjectMail;
  }

  private function getBodyMail() {
    switch($this->type) {
      // Registro de nueva cuenta (código de confirmación)
      case 0:
        $titleMail = "Confirmación de registro";
        $messageMail = "Aquí tienes tu código para confirmar tu cuenta y comenzar a añadir libros a tu estantería virtual:";
        break;
      // Recuperación de la cuenta (contraseña olvidada)
      case 1:
        $titleMail = "Contraseña temporal";
        $messageMail = "Aquí tienes una contraseña temporal para iniciar sesión con tu correo. Esta contraseña se borrará dentro de 15 minutos. <strong>¡No te olvides de cambiar la contraseña al entrar en tu cuenta!</strong>";
        break;
    }

    $bodyMessage = "
          <body style='margin:0;padding:0;'>
            <table style='width:100%;border-collapse:collapse;border:none;border-spacing:0;'>
              <tr>
                <td align='center' style='padding:0;'>
                  <table style='width:602px;border-collapse:collapse;border:3px solid #774360;border-spacing:0;text-align:left;background-color:#e7ab79'>
                    <tr>
                      <td align='center' style='padding:40px 0 30px 0;background:#4c3a51;'>
                        <a href='http://localhost:80'><img src='cid:heading_email' alt='Logo de BiblioPocket' width='300' style='height:auto;display:block;' />
                      </td>
                    </tr>
                    <tr>
                      <td style='padding:36px 30px 42px 30px;'>
                        <table style='width:100%;border-collapse:collapse;border:0;border-spacing:0;'>
                          <tr>
                            <td style='padding:0 0 0 0;color:#153643;'>
                              <h1 style='font-size:24px;margin:0 0 20px 0;'>".$titleMail."</h1>
                              <p style='margin:0 0 12px 0;font-size:16px;line-height:24px;'>".$messageMail."</p>
                              <p style='margin:0;font-size:20px;font-weight:bold;border:2px solid #774360;color:#774360;background-color:#fffdde;padding:10px;width: fit-content;margin:auto;text-align:center;'>".$this->customData."</p>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td style='padding:30px;background:#cd6781;'>
                        <table style='width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;'>
                          <tr>
                            <td style='padding:0;width:50%;' align='left'>
                              <p style='margin:0;font-size:14px;line-height:16px;color:#ffffff;font-weight:bold;font-style: italic;'>BiblioPocket 📚
                                <br/>
                                <small style='opacity:0.8;font-style:normal'>2024</small>
                              </p>
                            </td>
                            <td style='padding:0;width:50%;' align='right'>
                              <table style='border-collapse:collapse;border:0;border-spacing:0;'>
                                <tr>
                                  <td style='padding:0 0 0 10px;width:38px;'>
                                    <a href='http://www.twitter.com/' style='color:#ffffff;'><img src='https://assets.codepen.io/210284/tw_1.png' alt='Twitter' width='38' style='height:auto;display:block;border:0;' /></a>
                                  </td>
                                  <td style='padding:0 0 0 10px;width:38px;'>
                                    <a href='http://www.facebook.com/' style='color:#ffffff;'><img src='https://assets.codepen.io/210284/fb_1.png' alt='Facebook' width='38' style='height:auto;display:block;border:0;' /></a>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </body>
        ";

    return $bodyMessage;
  }
}