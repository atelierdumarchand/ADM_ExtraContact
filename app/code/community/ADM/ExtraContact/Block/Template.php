<?php

class ADM_ExtraContact_Block_Template extends Mage_Core_Block_Template
{
    public function getFormAction()
    {
        $formAction = parent::getFormAction();
        if($formAction === null) {
            return Mage::getUrl('*/*/');
        }
        return $formAction;
    }
    
    /**
     * Retrieve HTML for subjects dropdown
     * 
     * @return string
     */
    public function getSubjectsHtmlSelect()
    {
        return $this->getLayout()
            ->createBlock('core/html_select')
            ->setName('subject')
            ->setId('subject')
            ->setClass('validate-select subject')
            ->setOptions(Mage::helper('ExtraContact')->getSubjects())
            ->getHtml();
    }

    /**
     * Retrieve HTML for country dropdown
     * 
     * @return string
     */
    public function getCountryHtmlSelect()
    {
        return $this->getLayout()
            ->createBlock('core/html_select')
            ->setName('country')
            ->setId('country')
            ->setValue(Mage::getStoreConfig('general/country/default'))
            ->setOptions(Mage::helper('ExtraContact')->getCountryCollection()->toOptionArray())
            ->getHtml();
    }

    /**
     * Retrieve presentation
     * 
     * @return string
     */
    public function getPresentation()
    {
        switch (Mage::helper('ExtraContact')->getPresentationType()) {
            case '1':
                return $this->getLayout()
                    ->createBlock('cms/block')
                    ->setBlockId(Mage::helper('ExtraContact')->getPresentationCMS())
                    ->toHtml();
                break;
            case '2':
                return Mage::helper('ExtraContact')->getPresentationText();
                break;
            default:
                return '';
        }
    }
}
