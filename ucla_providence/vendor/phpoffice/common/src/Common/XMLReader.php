<?php

/**
 * This file is part of PHPOffice Common
 *
 * PHPOffice Common is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/Common/contributors.
 *
 * @see        https://github.com/PHPOffice/Common
 *
 * @copyright   2009-2016 PHPOffice Common contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\Common;

use DOMDocument;
use DOMXpath;
use ZipArchive;

/**
 * XML Reader wrapper
 *
 * @since   0.2.1
 */
class XMLReader
{
    /**
     * DOMDocument object
     *
     * @var \DOMDocument
     */
    private $dom;

    /**
     * DOMXpath object
     *
     * @var \DOMXpath
     */
    private $xpath;

    /**
     * Get DOMDocument from ZipArchive
     *
     * @param string $zipFile
     * @param string $xmlFile
     *
     * @return \DOMDocument|false
     *
     * @throws \Exception
     */
    public function getDomFromZip(string $zipFile, string $xmlFile)
    {
        if (file_exists($zipFile) === false) {
            throw new \Exception('Cannot find archive file.');
        }

        $zip = new \ZipArchive();
        $zip->open($zipFile);
        $content = $zip->getFromName($xmlFile);

        // Files downloaded from Sharepoint are somehow different and fail on the leading slash.
        if ($content === false && substr($xmlFile, 0, 1) === '/') {
            $content = $zip->getFromName(substr($xmlFile, 1));
        }

        $zip->close();

        if ($content === false) {
            return false;
        }

        return $this->getDomFromString($content);
    }

    /**
     * Get DOMDocument from content string
     *
     * @param string $content
     *
     * @return \DOMDocument
     */
    public function getDomFromString(string $content)
    {
        $originalLibXMLEntityValue = false;
        if (\PHP_VERSION_ID < 80000) {
            $originalLibXMLEntityValue = libxml_disable_entity_loader(true);
        }

        $this->dom = new \DOMDocument();
        $this->dom->loadXML($content);

        if (\PHP_VERSION_ID < 80000) {
            libxml_disable_entity_loader($originalLibXMLEntityValue);
        }

        return $this->dom;
    }

    /**
     * Get elements
     *
     * @param string $path
     * @param \DOMElement $contextNode
     *
     * @return \DOMNodeList<\DOMElement>
     */
    public function getElements(string $path, ?\DOMElement $contextNode = null)
    {
        if ($this->dom === null) {
            return new \DOMNodeList();
        }
        if ($this->xpath === null) {
            $this->xpath = new \DOMXpath($this->dom);
        }

        if (is_null($contextNode)) {
            return $this->xpath->query($path);
        }

        return $this->xpath->query($path, $contextNode);
    }

    /**
     * Registers the namespace with the DOMXPath object
     *
     * @param string $prefix The prefix
     * @param string $namespaceURI The URI of the namespace
     *
     * @return bool true on success or false on failure
     *
     * @throws \InvalidArgumentException If called before having loaded the DOM document
     */
    public function registerNamespace($prefix, $namespaceURI)
    {
        if ($this->dom === null) {
            throw new \InvalidArgumentException('Dom needs to be loaded before registering a namespace');
        }
        if ($this->xpath === null) {
            $this->xpath = new \DOMXpath($this->dom);
        }

        return $this->xpath->registerNamespace($prefix, $namespaceURI);
    }

    /**
     * Get element
     *
     * @param string $path
     * @param \DOMElement $contextNode
     *
     * @return \DOMElement|null
     */
    public function getElement($path, ?\DOMElement $contextNode = null): ?\DOMElement
    {
        $elements = $this->getElements($path, $contextNode);
        if ($elements->length > 0) {
            return $elements->item(0) instanceof \DOMElement ? $elements->item(0) : null;
        }

        return null;
    }

    /**
     * Get element attribute
     *
     * @param string $attribute
     * @param \DOMElement $contextNode
     * @param string $path
     *
     * @return string|null
     */
    public function getAttribute($attribute, ?\DOMElement $contextNode = null, ?string $path = null)
    {
        $return = null;
        if ($path !== null) {
            $elements = $this->getElements($path, $contextNode);
            if ($elements->length > 0) {
                /** @var \DOMElement $node Type hint */
                $node = $elements->item(0);
                $return = $node->getAttribute($attribute);
            }
        } else {
            if ($contextNode !== null) {
                $return = $contextNode->getAttribute($attribute);
            }
        }

        return ($return == '') ? null : $return;
    }

    /**
     * Get element value
     *
     * @param string $path
     * @param \DOMElement $contextNode
     *
     * @return string|null
     */
    public function getValue($path, ?\DOMElement $contextNode = null)
    {
        $elements = $this->getElements($path, $contextNode);
        if ($elements->length > 0) {
            return $elements->item(0)->nodeValue;
        }

        return null;
    }

    /**
     * Count elements
     *
     * @param string $path
     * @param \DOMElement $contextNode
     *
     * @return int
     */
    public function countElements($path, ?\DOMElement $contextNode = null)
    {
        $elements = $this->getElements($path, $contextNode);

        return $elements->length;
    }

    /**
     * Element exists
     *
     * @param string $path
     * @param \DOMElement $contextNode
     *
     * @return bool
     */
    public function elementExists($path, ?\DOMElement $contextNode = null)
    {
        return $this->getElements($path, $contextNode)->length > 0;
    }
}
