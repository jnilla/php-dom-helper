<?php
namespace Jnilla\PhpDomHelper;

use DOMDocument;
use Symfony\Component\CssSelector\CssSelectorConverter;
use DOMXPath;
use Joomla\Registry\Format\Ini;

/**
 * DOMDocument decorator class
 */
class DomHelper{
    /**
     * Creates a DOMDocument object, load HTML from a string and supress warnings
     * 
     * @param string $html
     *      The HTML to load into the document object
     * 
     * @return DOMDocument 
     *      The created document object
     */
    public static function loadHtml($html)
    {
        $dom = new DOMDocument('1.0','UTF-8');

        // Prepare the html
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Supressing errors
        libxml_use_internal_errors(true);

        // Load document
        $dom->LoadHTML($html, LIBXML_HTML_NOIMPLIED);

        // Init xpath
        self::initXpath($dom);

        // Return created document object
        return $dom;
    }

    /**
	 * Select nodes using CSS selectors
	 * 
     * @param DOMDocument $dom
     *      Document object
	 * @param string $selector
     *      CSS selector
	 * 
	 * @return DOMNodeList
     *      Selected nodes
	 */
    public static function querySelectorAll($dom, $selector)
    {
        // Init xpath object if needed
        self::initXpath($dom);

        // Execute CSS query and return matches
		return $dom->domHelper_xpath->query($dom->domHelper_cssToXpathConverter->toXPath($selector));
    }

    /**
     * Initialize the xpath object and store a reference inside the given document object
     * 
     * @param DOMDocument $dom
     *      Document object
     * 
     * @return void
     */
    private static function initXpath($dom)
    {
        // Init xpath object
		if(!isset($dom->domHelper_xpath)){
			$dom->domHelper_xpath = new DOMXPath($dom);
		}

        // Init cssToXpathConverter object
		if(!isset($dom->domHelper_cssToXpathConverter)){
            $dom->domHelper_cssToXpathConverter = new CssSelectorConverter();
        }
    }

    /**
     * Removes a node
     * 
     * @param DOMNode $node
     *      Node object
     * 
     * @return void
     */
    public static function removeNode($node)
    {
        $node->parentNode->removeChild($node);
    }

    /**
     * Returns a list of the elements of the class attribute
     * 
     * @param DOMNode $node
     *      Node object
     * 
     * @return void
     */
    public static function classList($node)
    {
        // Return empty array if class attirbute doesn't exist
        if(!$node->hasAttribute('class')){
            return [];
        }

        // Normalize classes
        $classes = strtolower($node->getAttribute('class'));

        // Create a list of classes
        $classes = preg_split('/\s+/i', $classes);

        // Return the list of classes
        return $classes;
    }

    /**
     * Adds the given classes to the list, omitting any that are already present
     * 
     * @param DOMNode $node
     *      Node object
     * @param string $classes
     *      Classes to add
     * 
     * @return void
     */
    public static function classListAdd($node, ...$classes)
    {
        // Get the node class list
        $classList = self::classList($node);

        // Add classes to the list
        foreach ($classes as $class) {
            // Normalize the class
            $class = strtolower(trim($class));

            // Skip if class exist
            if(in_array($class, $classList)){
                continue;
            }

            // Add the new class
            $classList[] = $class;
        }

        // Update the class attribute
        $classList = implode(' ', $classList);
        $node->setAttribute('class', $classList);
    }

    /**
     * Removes the given classes from the list
     * 
     * @param DOMNode $node
     *      Node object
     * @param string $classes
     *      Classes to remove
     * 
     * @return void
     */
    public static function classListRemove($node, ...$classes)
    {
        // Get the node class list
        $classList = self::classList($node);

        // Exit if class list is empty
        if(empty($classList)){
            return;
        }

        // Remove the classes from the class list
        foreach ($classes as $class) {
            // Normalize the class
            $class = strtolower(trim($class));

            // Remove class if exist
            $classKey = array_search($class, $classList);
            if($classKey !== false){
                unset($classList[$classKey]);
            }
        }

        // Update the class attribute
        $classList = implode(' ', $classList);
        $node->setAttribute('class', $classList);
    }

    /**
     * Insert a node after another node
     * 
     * @param DOMNode $nodeToInsert
     *      Node object to insert
     * @param DOMNode $referenceNode
     *      Reference node object
     * 
     * @return void
     */
    public static function after($nodeToInsert, $referenceNode)
    {
        $referenceNode->parentNode->insertBefore($nodeToInsert, $referenceNode->nextSibling);
    }

    /**
     * Insert a node before another node
     * 
     * @param DOMNode $nodeToInsert
     *      Node object to insert
     * @param DOMNode $referenceNode
     *      Reference node object
     * 
     * @return void
     */
    public static function before($nodeToInsert, $referenceNode)
    {
        $referenceNode->parentNode->insertBefore($nodeToInsert, $referenceNode);
    }

}




