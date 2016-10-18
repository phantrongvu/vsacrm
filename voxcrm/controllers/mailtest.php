<?php
function send_now()
{
    $this->load->library('email');

    $this->email->from('radon1284@yahoo.com', 'Ruel Nopal');
    $this->email->to('radon@radongrafix.com');

    $this->email->subject('Sending Email from CodeIgniter with Mandrill');
    $this->email->message('If you forgot how to do this, go ahead and refer to: <a href="http://the-amazing-php.blogspot.com/2015/05/codeigniter-send-email-with-mandrill.html">The Amazing PHP</a>.');

    $this->email->send();
}