<?php

class ADM_ExtraContact_Model_Adminhtml_System_Config_Source_Presentation_Origin
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => Mage::helper('adminhtml')->__('Aucune')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('adminhtml')->__('Bloc CMS')
            ),
            array(
                'value' => 2,
                'label' => Mage::helper('adminhtml')->__('Texte simple')
            )
        );
    }
}
