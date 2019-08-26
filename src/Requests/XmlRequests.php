<?php

namespace App\Requests;

/**
 * Class xml requests processing
 */
abstract class XmlRequests
{
    const PATH_REQUEST_XSD = null;
    const PATH_RESPONSE_XSD = null;

    /**
     * Validate request xml
     * @param string $xml
     * @return mixed
     */
    public function validateRequestXml(string $xml):bool
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        return $dom->schemaValidate(dirname(__DIR__) . get_called_class()::PATH_REQUEST_XSD);
    }

    /**
     * Validate response xml
     * @param string $xml
     * @return mixed
     */
    public function validateResponseXml(string $xml):bool
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        return $dom->schemaValidate(dirname(__DIR__) . get_called_class()::PATH_RESPONSE_XSD);
    }

    abstract function process(array $data);
}