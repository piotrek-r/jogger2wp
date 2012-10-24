<?php

namespace Jogger2Wp;

use DOMDocument;
use DOMXPath;
use DOMElement;

use DateTime;

/**
 * Parser.php
 *
 * @author Piotr Rybałtowski <piotrek@rybaltowski.pl>
 */
class Parser
{
    /**
     * @var string
     */
    protected $filepath = null;

    /**
     * @var array
     */
    protected $data = null;

    /**
     * @var DOMDocument
     */
    protected $doc = null;

    /**
     * @param string $filepath
     */
    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if(null === $this->data) {
            $doc = $this->getDoc();

            $xpath = new DOMXPath($doc);
            $entries = $xpath->query('//jogger/entry');

            $data = array();
            $data['entries'] = array();

            /** @var $entry DOMElement */
            foreach($entries as $entry) {

                $entryData = array(
                    'date' => new DateTime($this->getTagValue($entry, 'date')),
                    'levelId' =>  (int)$this->getTagValue($entry, 'level_id'),
                    'commentMode' => (int)$this->getTagValue($entry, 'comment_mode'),
                    'subject' => $this->getTagValue($entry, 'subject'),
                    'body' => $this->getTagValue($entry, 'body'),
                    'tags' => $this->extractTags($entry),
                    'permalink' => $this->getTagValue($entry, 'permalink'),
                    'category' => $this->getTagValue($entry, 'category'),
                    'comments' => $this->extractComments($entry),
                );

                $data['entries'][] = $entryData;

            }

            $this->data = $data;
        }
        return $this->data;
    }

    /**
     * @param \DOMElement $element
     * @param string $name
     * @return string
     */
    protected function getTagValue(DOMElement $element, $name)
    {
        return $element->getElementsByTagName($name)->item(0)->textContent;
    }

    /**
     * @param \DOMElement $element
     * @return array
     */
    protected function extractTags(DOMElement $element)
    {
        $tags = $this->getTagValue($element, 'tags');
        if(empty($tags)) {
            return array();
        }
        return explode(', ', $tags);
    }

    /**
     * @param \DOMElement $element
     * @return array
     */
    protected function extractComments(DOMElement $element)
    {
        $comments = array();

        foreach($element->getElementsByTagName('comment') as $comment) {
            $comments[] = array(
                'date' => new DateTime($this->getTagValue($comment, 'date')),
                'nick' => $this->getTagValue($comment, 'nick'),
                'nickUrl' => $this->getTagValue($comment, 'nick_url'),
                'body' => $this->getTagValue($comment, 'body'),
                'ip' => $this->getTagValue($comment, 'ip'),
            );
        }

        return $comments;
    }

    /**
     * @return \DOMDocument
     */
    public function getDoc()
    {
        if(null === $this->doc) {
            $this->doc = new DOMDocument('1.0', 'utf-8');
            $this->doc->load($this->filepath);
        }
        return $this->doc;
    }
}
