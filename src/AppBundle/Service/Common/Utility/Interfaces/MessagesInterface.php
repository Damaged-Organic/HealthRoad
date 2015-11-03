<?php
// AppBundle/Service/Common/Utility/Interfaces/MessagesInterface.php
namespace AppBundle\Service\Common\Utility\Interfaces;

interface MessagesInterface
{
    const MESSAGES_ROOT = 'messages';

    const MESSAGES_INFO    = 'info';
    const MESSAGES_WARNING = 'warning';
    const MESSAGES_SUCCESS = 'success';
    const MESSAGES_ERRORS  = 'errors';

    static public function getMessagesTypes();
}