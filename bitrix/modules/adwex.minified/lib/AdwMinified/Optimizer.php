<?php

namespace AdwMinified;

class Optimizer {
    /**
     * @var string
     */
    private $content;
    /**
     * @var \DiDom\Document
     */
    private $document;
    
    /**
     * Static constructor
     */
    public static function vd ($content, $name = '') {
        if ($_SERVER['REMOTE_ADDR'] === '83.221.215.239') {
            echo '<pre>';
            var_dump($content);
            echo '</pre>';
        }
    }
    
    /**
     * Static constructor
     */
    public static function minify (&$content) {
        $optimizer = new Optimizer($content);
        return $optimizer->getContent();
    }
    
    /**
     * Constructor
     */
    public function __construct(&$content) {
        $this->setContent($content);
        $this->site = new Site();
        if ($this->site->getIsAdminSection()) {
            return;
        }
        if (!$this->isHtml()) {
            return;
        }
        $this->removeTrash();
        $this->document = new Nokogiri($content);
    }
    
    private function removeTrash() {
        
    }
    
    /**
     * Check string is HTML
     *
     * @return array Links on styles
     */
    private function findStyleLink() {
        $linkHrefs = [];
        $links = $this->document->get('link[rel="stylesheet"]');
        foreach ($links as $link) {
            $linkHrefs[] = $link['href'];
        }
        return $linkHrefs;
    }
    
    /**
     * Check string is HTML
     *
     * @return bool
     */
    private function isHtml () {
        $isHtml = true;
        if (is_object(json_decode($this->content))) { // Is JSON?
            $isHtml = false;
        } elseif (stripos($this->content, '<!DOCTYPE') === false ) { // Is not Html
            $isHtml = false;
        }
        return $isHtml;
    }
    
    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = &$content;
    }
}