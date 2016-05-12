<?php
/**
 * User: Vitaly Kukin
 * Date: 03.03.2016
 * Time: 19:50
 */

function elp_mandrill(
    $api,
    $email_to,
    $subject,
    $content,
    $from_email = 'support@example.com',
    $from_name = 'Sample Site'
) {

    require_once(dirname(__FILE__) . '/Mandrill.php'); //Not required with Composer

    $type = true;

    $to = array();

    if( is_array($email_to) ){

        foreach($email_to as $mail)
            $to[] = array('email' => $mail, 'name' => '', 'type' => 'to');
    }
    else{
        $to[] = array('email' => $email_to, 'name' => '', 'type' => 'to');
    }

    try {
        $mandrill = new Mandrill($api);
        $message = array(
            'html'       => '',
            'text'       => '',
            'subject'    => $subject,
            'from_email' => $from_email,
            'from_name'  => $from_name,
            'to'         => $to,
            'metadata'   => array('website' => home_url('/')),
        );

        if($type) {
            $message['html'] = $content;
        } else {
            $message['text'] = $content;
        }

        $result = $mandrill->messages->send($message);

        return true;

    } catch(Mandrill_Error $e) {
        // Mandrill errors are thrown as exceptions
        error_log('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());

        return false;
    }
}