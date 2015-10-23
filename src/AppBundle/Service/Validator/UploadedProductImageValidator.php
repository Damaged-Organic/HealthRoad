<?php
// AppBundle/Service/Validator/UploadedProductImageValidator.php
namespace AppBundle\Service\Validator;

use Symfony\Component\Form\Form,
    Symfony\Component\Form\FormError,
    Symfony\Component\Translation\TranslatorInterface,
    Symfony\Component\Validator\Validator\RecursiveValidator;

use AppBundle\Entity\Product\ProductImage;

class UploadedProductImageValidator
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
        if( !$form->has('uploadedProductImages') )
            return TRUE;

        if( $form->get('uploadedProductImages')->isRequired() &&
            !array_filter($form->getData()->getUploadedProductImages()) ) {

            $form->get('uploadedProductImages')->addError(new FormError($this->_translator->trans('product.uploaded_product_images.not_blank', [], 'validators')));

            return FALSE;
        }

        foreach( $form->getData()->getUploadedProductImages() as $uploadedProductImage )
        {
            $productImage = (new ProductImage)->setImageProductFile($uploadedProductImage);

            $errors = $this->_validator->validate($productImage);

            if( count($errors) === 0 )
                return TRUE;

            foreach( $errors as $error ) {
                $form->get('uploadedProductImages')->addError(new FormError($error->getMessage()));
            }

            return FALSE;
        }

        return TRUE;
    }
}