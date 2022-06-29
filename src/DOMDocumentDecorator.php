<?php
namespace Jnilla\PhpDomDecorator;

use DOMDocument;
use Symfony\Component\CssSelector\CssSelectorConverter;
use DOMXPath;

/**
 * DOMDocument decorator class
 */
class DOMDocumentDecorator extends DOMDocument{
    
    private $xpath = null;
    private $cssToXpathConverter = null;

    /**
	 * Select nodes using CSS selectors
	 * 
	 * @param string $selector CSS selector
	 * 
	 * @return DOMNodeList Selected nodes
	 */
    public function querySelectorAll($selector)
    {
        // Init xpath object
		if(!isset($this->xpath)){
			$this->xpath = new DOMXPath($this);
		}

        // Init cssToXpathConverter object
		if(!isset($this->cssToXpathConverter)){
            $this->cssToXpathConverter = new CssSelectorConverter();
        }

        // Execute CSS query and return matches
		return $this->xpath->query($this->cssToXpathConverter->toXPath($selector));
    }
}




