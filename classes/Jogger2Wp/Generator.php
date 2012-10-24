<?php

namespace Jogger2Wp;

use DOMDocument;
use DOMElement;
use DOMText;

/**
 * Generator.php
 *
 * @author Piotr Rybałtowski <piotrek@rybaltowski.pl>
 */
class Generator
{
    // region vars

    /**
     * @var array
     */
    protected $data = null;

    // endregion

    // region constructor

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // endregion

    // region generator

    /**
     * @return \DOMDocument
     */
    public function generate()
    {
        $data = $this->getData();

        $doc = new DOMDocument('1.0', 'utf-8');
        $channel = $doc->createElement('channel');
        $doc->appendChild($channel);

        foreach($data['entries'] as $entry) {
            $item = $doc->createElement('item');

            $this->createElementAndAppend($doc, $item, 'title', $entry['subject']);
            $this->createElementAndAppend($doc, $item, 'pubDate', $entry['date']->format('r'));
            $this->createElementAndAppend($doc, $item, 'content:encoded', $entry['body']);

            $channel->appendChild($item);
        }

        return $doc;
    }

    /**
     * @param \DOMDocument $doc
     * @param \DOMElement $parent
     * @param string $tagName
     * @param string $content
     * @return Generator
     */
    protected function createElementAndAppend(DOMDocument $doc, DOMElement $parent, $tagName, $content)
    {
        $element = $doc->createElement($tagName);
        $element->appendChild(new DOMText(trim($content)));
        $parent->appendChild($element);
        return $this;
    }

    // endregion

    // region getters

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    // endregion
}
