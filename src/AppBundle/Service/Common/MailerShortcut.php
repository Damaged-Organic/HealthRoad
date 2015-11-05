<?php
// AppBundle/Service/Common/MailerShortcut.php
namespace AppBundle\Service\Common;

use Swift_Mailer,
    Swift_Message,
    Swift_Attachment;

class MailerShortcut
{
    private $_mailer;

    public function setMailer(Swift_Mailer $mailer)
    {
        $this->_mailer = $mailer;
    }

    public function sendMail($from, $to, $subject, $body, $attachment = NULL)
    {
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, 'text/html')
        ;

        if( $attachment )
            $message = $this->attach($message, $attachment);

        return ( $this->_mailer->send($message) ) ? TRUE : FALSE;
    }

    public function attach(Swift_Message $message, $attachment)
    {
        if( !is_array($attachment) ) {
            if( $attachment instanceof Swift_Attachment )
                $message->attach($attachment);
        } else {
            foreach($attachment as $resource) {
                if( $resource instanceof Swift_Attachment ) {
                    $message->attach($resource);
                }
            }
        }

        return $message;
    }
}