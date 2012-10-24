<?php

namespace Jogger2Wp;

/**
 * Application.php
 *
 * @author Piotr Rybałtowski <piotrek@rybaltowski.pl>
 */
class Application
{
    // region vars

    /**
     * @var array
     */
    protected $argv = null;

    // endregion

    // region constructor

    /**
     * @param array $argv
     */
    public function __construct($argv)
    {
        $this->argv = $argv;
    }

    // endregion

    // region run

    /**
     * @return int
     */
    public function run()
    {
        if(!$this->checkArgv()) return $this->usage();

        $sourceXmlPath = $this->argv[1];
        $outputXmlPath = $this->argv[2];

        if(!is_readable($sourceXmlPath)) {
            printf("File %s is not readable\n", $sourceXmlPath);
            return 10;
        }

        if(file_exists($outputXmlPath)) {
            if(!is_writable($outputXmlPath)) {
                printf("File %s is not writable\n", $outputXmlPath);
                return 20;
            }
        }
        else {
            $outputXmlDir = dirname($outputXmlPath);
            if(!is_writable($outputXmlDir)) {
                printf("Directory of file %s is not writable\n", $outputXmlPath);
                return 21;
            }
        }

        $xml = $this->generateXml($this->parseXml($sourceXmlPath));

        var_dump($xml->saveXML());

        return 0;
    }

    // endregion

    // region load/parse xml

    /**
     * @param string $sourceXmlPath
     * @return array
     */
    public function parseXml($sourceXmlPath)
    {
        return (new Parser($sourceXmlPath))->getData();
    }

    // endregion

    // region generate new xml

    /**
     * @param array $data
     * @return \DOMDocument
     */
    public function generateXml(array $data)
    {
        return (new Generator($data))->generate();
    }

    // endregion

    // region console tools

    /**
     * @return boolean
     */
    public function checkArgv()
    {
        if(count($this->argv) !== 3) {
            return false;
        }
        return true;
    }

    /**
     * @return int
     */
    public function usage()
    {
        printf("Usage: %s <sourceXmlFile> <outputXmlFile>\n", $this->argv[0]);
        return 1;
    }

    // endregion
}
