<?php
// AppBundle/Service/Common/Messages.php
namespace AppBundle\Service\Common;

use Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Translation\TranslatorInterface;

use AppBundle\Service\Common\Utility\Interfaces\MessagesInterface;

class Messages implements MessagesInterface
{
    private $_session;
    private $_translator;

    public function setSession(Session $session)
    {
        $this->_session = $session;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    public function markCreateSuccess()
    {
        $this->_session->getFlashBag()->add(self::MESSAGES_ROOT, [
            self::MESSAGES_SUCCESS => [$this->_translator->trans('common.success.create', [], 'responses')]
        ]);
    }

    public function markUpdateSuccess()
    {
        $this->_session->getFlashBag()->add(self::MESSAGES_ROOT, [
            self::MESSAGES_SUCCESS => [$this->_translator->trans('common.success.update', [], 'responses')]
        ]);
    }

    public function markDeleteSuccess()
    {
        $this->_session->getFlashBag()->add(self::MESSAGES_ROOT, [
            self::MESSAGES_SUCCESS => [$this->_translator->trans('common.success.delete', [], 'responses')]
        ]);
    }

    public function markBindSuccess($message)
    {
        $this->_session->getFlashBag()->add(self::MESSAGES_ROOT, [
            self::MESSAGES_SUCCESS => [$message]
        ]);
    }

    public function markUnbindSuccess($message)
    {
        $this->_session->getFlashBag()->add(self::MESSAGES_ROOT, [
            self::MESSAGES_SUCCESS => [$message]
        ]);
    }

    public function markReplenishSuccess()
    {
        $this->_session->getFlashBag()->add(self::MESSAGES_ROOT, [
            self::MESSAGES_SUCCESS => [$this->_translator->trans('student_balance.success', [], 'responses')]
        ]);
    }

    public function markReplenishErrors(array $errors)
    {
        $this->_session->getFlashBag()->add(self::MESSAGES_ROOT, [
            self::MESSAGES_ERRORS => $errors
        ]);
    }
}