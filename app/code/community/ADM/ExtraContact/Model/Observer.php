<?php

class ADM_ExtraContact_Model_Observer extends Varien_Event_Observer
{
    /**
     * Get Captcha String
     *
     * @param Varien_Object $request            
     * @param string $formId            
     * @return string
     *
     */
    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }

    /**
     * Break the execution in case of incorrect CAPTCHA
     *
     * @param Varien_Event_Observer $observer            
     * @return ExtraContact_Model_Frontend_Observer
     *
     */
    public function checkCaptcha($observer)
    {
        $formId = 'ExtraContact_form';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (! $captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
            }
        }

        return $this;
    }
    
    /**
     * Insert Block
     * 
     * @param Varien_Event_Observer $observer     
     */
    public function insertBlock($observer)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        if ($block->getNameInLayout() == 'customer_account_dashboard_top') {
            $this->insertBlockAfterCustomerAccountDashboardTop($block);
        }
    }
    
    /**
     * Insert block after customer_account_dashboard_top layout
     * @param Mage_Core_Block_Abstract $block
     */
    protected function insertBlockAfterCustomerAccountDashboardTop($block)
    {
        $child = clone $block;
        $child->setNameInLayout('customer_account_dashboard_top_extra');
        $block->setChild('child', $child);
        $block->setTemplate('ExtraContact/dashboard.phtml');
    }
    
    /**
     * 
     * @param Varien_Event_Observer $observer
     */
    public function hookToControllerActionPreDispatch($observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        if($controller->getFullActionName() == 'customer_account_index') {
            $this->onCustomerAccountIndexPost($controller->getRequest());
        }
    }
    
    /**
     * Send mail in POST context
     * @param Mage_Core_Controller_Request_Http $request
     */
    protected function onCustomerAccountIndexPost($request)
    {
        if($request->isPost() && $request->getPost('extracontact') !== null) {
            Mage::helper('ExtraContact')->postMail($request);
        } 
    }
}