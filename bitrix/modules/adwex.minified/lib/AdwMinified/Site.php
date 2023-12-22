<?php

namespace AdwMinified;

class Site {
    /**
     * @var bool
     */
    private $isAdminSection;
    private $isPageSpeed;
    private $siteId;
    
    /**
     * Constructor
     */
    public function __construct() {
    }
    
    /**
     * @param bool $value
     */
    private final function setIsAdminSection ($value) {
        $this->isAdminSection = $value;
    }
    
    /**
     * @return bool
     */
    public final function getIsAdminSection () {
        if ($this->isAdminSection === null) {
            $this->setIsAdminSection($this->isAdminSection());
        }
        return $this->isAdminSection;
    }
    
    /**
     * @param bool $value
     */
    private final function setIsPageSpeed ($value) {
        $this->isPageSpeed = $value;
    }
    
    /**
     * @return bool
     */
    public final function getIsPageSpeed () {
        if ($this->isPageSpeed === null) {
            $this->setIsPageSpeed($this->isPageSpeed());
        }
        return $this->isPageSpeed;
    }
    
    /**
     * @param string $value
     */
    private final function setSiteId ($value) {
        $this->siteId = $value;
    }
    
    /**
     * @return string
     */
    public final function getSiteId () {
        if ($this->siteId === null) {
            $this->setSiteId($this->getCurrentSiteId());
        }
        return $this->siteId;
    }
    
    /**
     * Return true if is Chrome Lighthouse
     *  
     * @return bool
     */
    private final function isPageSpeed () {
        return (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') !== false);
    }
    
    /**
     * Return true if is admin section
     *  
     * @return bool
     */
    private final function isAdminSection () {
        return (defined('ADMIN_SECTION') && ADMIN_SECTION === true);
    }
    
    /**
     * Return Id current site
     *  
     * @return string
     */
    private final function getCurrentSiteId () {
		$siteID = SITE_ID;
		if ($this->isAdminSection) {
	        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/mainpage.php');
	        $siteID = (new \CMainPage)->GetSiteByHost();
	        if (!$siteID) {
	            $siteID = 's1';
            }
        }
        return $siteID;
    }
    
}