<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Emailconfig
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        $config['protocol']    = $this->CI->st->protocol;
        $config['smtp_host']    = $this->CI->st->smtp_host;
        $config['smtp_port']    = $this->CI->st->smtp_port;
        $config['smtp_timeout'] = '7';
        $config['smtp_user']    = $this->CI->st->smtp_user;
        $config['smtp_pass']    = $this->CI->st->smtp_pwd;
        $config['charset']    = 'iso-8859-1';
        $config['mailtype'] = 'text';
        $config['validation'] = TRUE;

        $this->CI->load->library('email', $config);
        $this->CI->email->set_newline("\r\n");
    }

    //support us email
    public function support_mail($name, $user_mail, $bdy)
    {
        if ($user_mail) {
            $subj = "Support message from " . $user_mail;
        } else if (!$user_mail) {
            $subj = "Support Mail";
        }

        $this->CI->email->from('jvweedtest@gmail.com', 'Rating');
        $this->CI->email->to('john.nktech@gmail.com');
        $this->CI->email->subject($subj);
        $this->CI->email->message($bdy);

        if ($this->CI->email->send()) {
            return true;
        } else {
            return $this->CI->email->print_debugger();
        }
    }

    //verification code on registration
    public function send_email_code($email, $uname, $act_key, $link)
    {
        $body = "Hello " . $uname . "\n\nYour verification code is " . $act_key . "\nEnter the above code to verify your account.\nClick here " . $link . "\n\nIf you have any questions, send us an email at info@nktech.in.\n\nBest Regards,\nNKTECH\nhttps://nktech.in";

        $this->CI->email->from('jvweedtest@gmail.com', 'Rating');
        $this->CI->email->to($email);
        $this->CI->email->subject("Verification Code");
        $this->CI->email->message($body);

        if ($this->CI->email->send()) {
            return true;
        } else {
            return $this->CI->email->print_debugger();
        }
    }

    //verification code for resetting password
    public function resetpassword_vcode($email, $act_key, $userid)
    {
        $body = "Your verification code is " . $act_key . "\nEnter the above code to reset your password.\n\nSend us an email at info@nktech.in for any queries.\n\nBest Regards,\nNKTECH\nhttps://nktech.in";

        $this->CI->email->from('jvweedtest@gmail.com', 'Rating');
        $this->CI->email->to($email);
        $this->CI->email->subject("Password Reset - Verification Code");
        $this->CI->email->message($body);

        if ($this->CI->email->send()) {
            return true;
        } else {
            return $this->CI->email->print_debugger();
        }
    }

    //new password from resetting password
    public function resetpassword($user_email, $rspwd, $user_name)
    {
        $body = "Hello " . $user_name . ", Your new password is " . $rspwd . "\n\nSend us an email at info@nktech.in for any queries.\n\nBest Regards,\nNKTECH\nhttps://nktech.in";

        $this->CI->email->from('jvweedtest@gmail.com', 'Rating');
        $this->CI->email->to($user_email);
        $this->CI->email->subject("Password Reset");
        $this->CI->email->message($body);

        if ($this->CI->email->send()) {
            return true;
        } else {
            return $this->CI->email->print_debugger();
        }
    }

}
