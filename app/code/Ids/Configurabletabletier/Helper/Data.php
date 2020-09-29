<?php

namespace Ids\Configurabletabletier\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_ACTIVE = 'idsconfigurabletabletier/general/active';
    const XML_PATH_ATTRIBUTE_SETS = 'idsconfigurabletabletier/general/attribute_sets';
    const XML_PATH_ATTRIBUTES_TO_HIDE = 'idsconfigurabletabletier/general/attributes_to_hide';
    const XML_PATH_SHOW_INFO_WINDOW = 'idsconfigurabletabletier/general/show_info_window';
    const XML_PATH_BTN_TEXT_TO_OPEN_INFO_WINDOW = 'idsconfigurabletabletier/general/btn_text_to_open_info_window';
    const XML_PATH_CMS_INFO_WINDOW = 'idsconfigurabletabletier/general/cms_info_window';

    /**
     * Get active status from configuration
     *
     * @return mixed
     */
    public function getActiveStatusFromConfig() {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is Module Active
     *
     * @return bool
     */
    public function isModuleActive() {
        return $this->getActiveStatusFromConfig() == 1;
    }


    /**
     * Get attribute sets from configuration
     *
     * @return array
     */
    public function geAttributeSetsFromConfig() {
        $attributeSets = array();

        $attributeSetsString = $this->scopeConfig->getValue(self::XML_PATH_ATTRIBUTE_SETS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if(strlen($attributeSetsString) > 0) {
            $attributeSetsExploded = explode(',', $attributeSetsString);

            if(is_array($attributeSetsExploded) && count($attributeSetsExploded) > 0) {
                $attributeSets = $attributeSetsExploded;
            }
        }

        return $attributeSets;
    }


    /**
     * Get attributes to hide from configuration
     *
     * @return array
     */
    public function geAttributesToHideFromConfig() {
        $attributesToHide = array();

        $attributesToHideString = $this->scopeConfig->getValue(self::XML_PATH_ATTRIBUTES_TO_HIDE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if(strlen($attributesToHideString) > 0) {
            $attributesToHideExploded = explode(',', $attributesToHideString);

            if(is_array($attributesToHideExploded) && count($attributesToHideExploded) > 0) {
                $attributesToHide = $attributesToHideExploded;
            }
        }

        return $attributesToHide;
    }


    /**
     * Get show info window status from configuration
     *
     * @return mixed
     */
    public function getShowInfoWindowsStatusFromConfig() {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_SHOW_INFO_WINDOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get button text to open info window from configuration
     *
     * @return mixed
     */
    public function getBtnTextToOpenInfoWindowFromConfig() {
        return trim($this->scopeConfig->getValue(self::XML_PATH_BTN_TEXT_TO_OPEN_INFO_WINDOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    /**
     * Get CMS block ID with content to show in info window from configuration
     *
     * @return mixed
     */
    public function getCmsBlockIdWithContentToShowInInfoWindowFromConfig() {
        return trim($this->scopeConfig->getValue(self::XML_PATH_CMS_INFO_WINDOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

}