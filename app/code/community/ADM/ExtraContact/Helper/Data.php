<?php

class ADM_ExtraContact_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'contacts/ExtraContact/enabled';
    const XML_PATH_COUNTRY_MANAGEMENT_ENABLED = 'contacts/ExtraContact/country';
    const XML_PATH_ORDER_MANAGEMENT_ENABLED = 'contacts/ExtraContact/order';
    const XML_PATH_CAPTCHA_ENABLED = 'contacts/ExtraContact/captcha';
    const XML_PATH_CONFIG = 'contacts/ExtraContact/config';
    const XML_PATH_PRESENTATION = 'contacts/ExtraContact/presentation';
    const XML_PATH_PRESENTATION_CMS = 'contacts/ExtraContact/presentationCMS';
    const XML_PATH_PRESENTATION_TEXT = 'contacts/ExtraContact/presentationText';
    const XML_PATH_ACKNOWLEDGMENT_ENABLED = 'contacts/ExtraContact/acknowledgment';
    const XML_PATH_EMAIL_RECIPIENT = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER = 'contacts/email/sender_email_identity';
    const XML_EMAIL_TEMPLATE_ID = 'ExtraContact_email_template';
    const XML_EMAIL_ACKNOWLEDGMENT_TEMPLATE_ID = 'ExtraContact_email_acknowledgment_template';
    
    protected $_countryCollection;

    /**
     * Get country collection
     * 
     * @return mixed
     */
    public function getCountryCollection()
    {
        if ($this->_countryCollection === null) {
            $this->_countryCollection = Mage::getResourceModel('directory/country_collection')->loadByStore();
        }
        return $this->_countryCollection;
    }
    
    /**
     * Get email sender from Mage_Contacts module
     *
     * @return return
     */
    public function getDefaultEmailSender()
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER);
    }
    
    /**
     * Get email recipient from Mage_Contacts module
     * 
     * @return return
     */
    public function getDefaultEmailRecipient()
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT);
    }

    /**
     * Check if the module is enabled
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    /**
     * Get presenation type
     * Values :
     * 0 => nothing
     * 1 => Block CMS
     * 2 => Simple Text
     * 
     * @return string
     */
    public function getPresentationType()
    {
        return Mage::getStoreConfig(self::XML_PATH_PRESENTATION);
    }

    /**
     * Get CMS block ID
     * 
     * @return string
     */
    public function getPresentationCMS()
    {
        return Mage::getStoreConfig(self::XML_PATH_PRESENTATION_CMS);
    }

    /**
     * Get presentation text
     * 
     * @return string
     */
    public function getPresentationText()
    {
        return Mage::getStoreConfig(self::XML_PATH_PRESENTATION_TEXT);
    }

    /**
     * Check if country management is enabled
     * 
     * @return boolean
     */
    public function countryManagementIsEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_COUNTRY_MANAGEMENT_ENABLED);
    }

    /**
     * Check if country should be displayed
     * 
     * @return boolean
     */
    public function activateCountry()
    {
        return $this->countryManagementIsEnabled() && $this->getCountryCollection()->count() > 0;
    }

    /**
     * Check if order management is enabled
     * 
     * @return boolean
     */
    public function orderManagementIsEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_ORDER_MANAGEMENT_ENABLED);
    }
    
    /**
     * Check if acknowledgment management is enabled
     *
     * @return boolean
     */
    public function acknowledgmentIsEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_ACKNOWLEDGMENT_ENABLED);
    }

    /**
     * Check if captcha is enabled
     * 
     * @return boolean
     */
    public function captchaIsEnabled()
    {
        return (bool) Mage::getStoreConfig(self::XML_PATH_CAPTCHA_ENABLED);
    }

    /**
     * Get configuration
     * 
     * @return mixed
     */
    public function getConfiguration()
    {
        return Mage::helper('core')->jsonDecode(Mage::getStoreConfig(self::XML_PATH_CONFIG));
    }
    
    /**
     * Get email introductions
     * 
     * @return string
     */
    public function getEmailIntroductions()
    {
        $intros = array();
        $configuration = $this->getConfiguration();
        foreach($configuration as $key => $config) {
            $intros[$key] = isset($config['content']) ? $config['content'] : '';
        }
        return Mage::helper('core')->jsonEncode($intros);
    }

    /**
     * Get subjects
     * 
     * @return mixed
     */
    public function getSubjects()
    {
        $configuration = $this->getConfiguration();
        $list = array();
        foreach ($configuration as $item) {
            $list[] = $item['subject'];
        }
        return $list;
    }

    /**
     * Get user name
     * 
     * @return string
     */
    public function getUserName()
    {
        if (! Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        return trim(Mage::getSingleton('customer/session')->getCustomer()->getName());
    }
    
    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId()
    {
        if (! Mage::getSingleton('customer/session')->isLoggedIn()) {
            return null;
        }
        return (int) Mage::getSingleton('customer/session')->getCustomer()->getId();
    }

    /**
     * Get user email
     * 
     * @return string
     */
    public function getUserEmail()
    {
        if (! Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        return Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    }
    
    public function postMail($request)
    {
        $post = $request->getPost();
        if ($post) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
        
            try {
                $error = false;
                $datas = array();
        
                if (isset($post['comment'])) {
                    $datas['comment'] = Mage::helper('core')->stripTags(trim($post['comment']));
                }
                if (isset($post['name'])) {
                    $datas['name'] = Mage::helper('core')->stripTags(trim($post['name']));
                }
                if (isset($post['email'])) {
                    $datas['email'] = Mage::helper('core')->stripTags(trim($post['email']));
                }
        
                $postObject = new Varien_Object();
                if (! empty($post['order'])) {
                    $datas['order'] = Mage::helper('core')->stripTags(trim($post['order']));
        
                    if (! Zend_Validate::is($post['order'], 'Int')) {
                        $error = true;
                    } elseif (Mage::getModel('sales/order')->loadByIncrementId($datas['order'])->entity_id !== null) {
                        $datas['orderLink'] = Mage::getModel('core/url')->getUrl(
                            'adminhtml/sales_order/view', array(
                                '_current' => false,
                                'order_id' => Mage::getModel('sales/order')->loadByIncrementId($datas['order'])->entity_id
                            )
                        );
                    }
                }
        
                $subjectId = null;
                if (! isset($post['subject'])) {
                    $error = true;
                } else {
                    $subjectId = (int) $post['subject'];
        
                    $configuration = Mage::helper('ExtraContact')->getConfiguration();
        
                    if (! Zend_Validate::is($datas['comment'], 'NotEmpty') ||
                        ! Zend_Validate::is($datas['email'], 'EmailAddress') ||
                        ! isset($configuration[$subjectId])) {
                        $error = true;
                    }
                }
        
                if ($error) {
                    throw new Exception();
                }
        
                if (! empty($post['country'])) {
                    $datas['countryName'] = Mage::app()->getLocale()->getCountryTranslation($post['country']);
                }
        
                $datas['subjectName'] = $configuration[$subjectId]['subject'];
        
                $emailRecipient = Mage::helper('ExtraContact')->getDefaultEmailRecipient();
                if (isset($configuration[$subjectId]['destination']) &&
                Zend_Validate::is($configuration[$subjectId]['destination'], 'EmailAddress')) {
                    $emailRecipient = $configuration[$subjectId]['destination'];
                }
        
                $userId = Mage::helper('ExtraContact')->getUserId();
                if ($userId !== null) {
                    $datas['profilLink'] = Mage::getModel('core/url')->getUrl(
                        'adminhtml/customer/edit/',
                        array(
                            'id' => $userId,
                            'key' => Mage::getSingleton('adminhtml/url')
                            ->getSecretKey('adminhtml_customer_edit', '/edit/id/1/')
                        )
                    );
                }
        
                $postObject->setData($datas);
        
                $mailTemplate = Mage::getModel('core/email_template');
                $mailTemplate
                    ->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        self::XML_EMAIL_TEMPLATE_ID,
                        Mage::helper('ExtraContact')->getDefaultEmailSender(),
                        $emailRecipient,
                        null,
                        array('data' => $postObject)
                    );
        
                if (! $mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }
        
                if (Mage::helper('ExtraContact')->acknowledgmentIsEnabled()) {
                    $mailTemplateAcknow = Mage::getModel('core/email_template');
                    $mailTemplateAcknow
                        ->setDesignConfig(array('area' => 'frontend'))
                        ->sendTransactional(
                            self::XML_EMAIL_ACKNOWLEDGMENT_TEMPLATE_ID,
                            Mage::helper('ExtraContact')->getDefaultEmailSender(),
                            $post['email'],
                            null,
                            array('data' => $postObject)
                        );

                    if (! $mailTemplateAcknow->getSentSuccess()) {
                        throw new Exception();
                    }
                }
        
                $translate->setTranslateInline(true);
        
                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('ExtraContact')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('ExtraContact')->__('Unable to submit your request. Please, try again later'));
            }
        }
    }
}
