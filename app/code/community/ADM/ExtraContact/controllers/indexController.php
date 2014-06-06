<?php

class ADM_ExtraContact_indexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        
        if (! Mage::helper('ExtraContact')->isEnabled()) {
            $this->norouteAction();
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()
            ->getBlock('ExtraContactForm')
            ->setDisplayMessageBlock(true)
            ->setFormAction(Mage::getUrl('*/*/post'));

        $this->_initLayoutMessages('customer/session');
        
        $this->renderLayout();
    }

    public function postAction()
    {
        Mage::helper('ExtraContact')->postMail($this->getRequest());
        $this->_redirect('*/*/');
    }
}
