<?php
namespace Mido;
/**
 * Mido Mail class
 *
 * @package Mido
 */

class Mail {

    public function __construct(){

        $this->options = self::get_options();
        return $this;

    }

    public static function init(){

        add_action('phpmailer_init', array('MidoMail','init_smtp'));
        if(is_admin()){
            \Mido\MailAdmin::init();
        }
    }

    private static function get_options(){


          $options = get_option('mido_mail_options');
          $options['password'] = self::get_password($options);
          $options['template'] = false;
          return $options;
    }

    public static function get_password($options = false){

      if(!$options){
          $options = get_option('mido_mail_options');
      }

      $temp_password = $options['password'];
      $password = "";
      $decoded_pass = base64_decode($temp_password);
      /* no additional checks for servers that aren't configured with mbstring enabled */
      if (!function_exists('mb_detect_encoding')) {
          return $decoded_pass;
      }
      /* end of mbstring check */
      if (base64_encode($decoded_pass) === $temp_password) {  //it might be encoded
          if (false === mb_detect_encoding($decoded_pass)) {  //could not find character encoding.
              $password = $temp_password;
          } else {
              $password = base64_decode($temp_password);
          }
      } else { //not encoded
          $password = $temp_password;
      }
      return $password;

    }

    public function getSender(){
        return array(email=>$this->options['from_email'],name=>$this->options['from_name']);
    }
    public function smtp($custom_options){
          $this->options = array_merge($this->options, $custom_options);
          return $this;
    }

    public static function init_smtp($phpmailer){

        $options = self::get_options();
        $phpmailer->IsSMTP();
        $from_email = $options['from_email'];
        $phpmailer->From = $from_email;
        $from_name = $options['from_name'];
        $phpmailer->FromName = $from_name;
        $phpmailer->SetFrom($phpmailer->From, $phpmailer->FromName);

        /* Set the SMTPSecure value */
        if ($mido_mail_options['type_encryption'] !== 'none') {
            $phpmailer->SMTPSecure = $mido_mail_options['type_encryption'];
        }

        /* Set the other options */
        $phpmailer->Host = $options['host'];
        $phpmailer->Port = $options['port'];


        if ('yes' == $options['autentication']) {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $options['username'];
            $phpmailer->Password = $options['password'];
        }



        //PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
        $phpmailer->SMTPAutoTLS = false;
    }

    public function template($template){

      $this->options['template']=$template;

      return $this;

    }

    private function compile($data){

      $template = $this->options['template'];

      if(!$template) return $data;

      if(!is_string($template)){

          $template = 'mail.twig';

      }

      if(is_string($data)){
          $data['main'] = $data;
      }

      $compiled = Mido::compile($template, $data);

      return $compiled;

    }

    public function send($to_email, $subject, $message, $template = false) {

        $errors = '';


        if($template){
            $this->options['template'] = $template;
        }

        require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
        $mail = new \PHPMailer();

        $charset = get_bloginfo('charset');
        $mail->CharSet = $charset;

        $from_name = $this->options['from_name'];
        $from_email = $this->options['from_email'];

        $mail->IsSMTP();

        /* If using smtp auth, set the username & password */
        if ('yes' == $this->options['autentication']) {
            $mail->SMTPAuth = true;
            $mail->Username = $this->options['username'];
            $mail->Password = $this->options['password'];
        }



         //Set the SMTPSecure value, if set to none, leave this blank */
        if ($this->options['type_encryption'] !== 'none') {
            $mail->SMTPSecure = $this->options['type_encryption'];
        }

        /* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
        $mail->SMTPAutoTLS = false;



        /* Set the other options */
        $mail->Host = $this->options['host'];
        $mail->Port = $this->options['port'];
        $mail->SetFrom($from_email, $from_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->MsgHTML($this->compile($message));
        $mail->AddAddress($to_email);
        $mail->SMTPDebug = 0;


        /* Send mail and return result */
        if (!$mail->Send())
            $errors = $mail->ErrorInfo;

        $mail->ClearAddresses();
        $mail->ClearAllRecipients();

        if (!empty($errors)) {
            return $errors;
        } else {
            return true;
        }
    }



}
