<?php
// AppBundle/Service/Validator/UploadedSupplierImageValidator.php
namespace AppBundle\Service\Validator;

use Symfony\Component\Form\Form,
    Symfony\Component\Form\FormError,
    Symfony\Component\Translation\TranslatorInterface,
    Symfony\Component\Validator\Validator\RecursiveValidator;

use AppBundle\Entity\Supplier\SupplierImage;

class UploadedSupplierImageValidator
{
    protected $_validator;
    protected $_translator;

    public function setValidator(RecursiveValidator $validator)
    {
        $this->_validator = $validator;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    public function validate(Form $form)
    {
        if( !$form->has('uploadedSupplierImages') )
            return TRUE;

        foreach( $form->getData()->getUploadedSupplierImages() as $uploadedSupplierImage )
        {
            $supplierImage = (new SupplierImage)->setImageSupplierFile($uploadedSupplierImage);

            $errors = $this->_validator->validate($supplierImage);

            if( count($errors) === 0 )
                return TRUE;

            foreach( $errors as $error ) {
                $form->get('uploadedSupplierImages')->addError(new FormError($error->getMessage()));
            }

            return FALSE;
        }

        return TRUE;
    }
}