<?php

namespace App\Service;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use App\Requests\Nack;

class XmlService
{
    private $container;
    private $requests;
    private $encoder;

    /**
     * XmlService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->requests = $this->container->getParameter('requests');

        $this->encoder = new XmlEncoder();
        $serializer = new Serializer(array(new CustomNormalizer()), array('xml' => new XmlEncoder()));
        $this->encoder->setSerializer($serializer);
    }

    /**
     * Processing request xml
     * @param string $xml
     * @return string
     */
    public function processing(string $xml):string
    {
        try {
            return $this->prepareResponse($xml);
        } catch (\Exception $e) {
            return $this->prepareErrorResponse($xml, $e->getMessage());
        }
    }

    /**
     * Get type of request from encoded xml
     * @param array $data
     * @return string
     * @throws \Exception
     */
    private function getType(array $data):string
    {
        if (isset($data['header']['type'])) {
            return $data['header']['type'];
        } else {
            //error no request type node
            throw new \Exception('Wrong xml format: no request type.');
        }
    }

    /**
     * Prepare response xml
     * @param string $xml
     * @return string
     * @throws \Exception
     */
    private function prepareResponse(string $xml):string
    {

        $encoded = $this->encoder->decode($xml, 'xml');

        $type = $this->getType($encoded);

        if (in_array($type, array_keys($this->requests))) {
            //create request object by request type
            $request = new $this->requests[$type]();

            //validate xml
            if (!$request->validateRequestXml($xml)) {
                throw new \Exception('The requested message is not recognized.');
            }

            list($response, $rootNodeName) = $request->process($encoded);

            $responseXml = $this->encoder->encode($response, 'xml', ['xml_root_node_name' => $rootNodeName, 'xml_encoding' => 'UTF-8']);

            //validate xml
            if (!$request->validateResponseXml($responseXml)) {
                throw new \Exception('The response contains an error structure.');
            }

            return $responseXml;
        } else {
            //error wrong request type
            throw new \Exception('Wrong xml format: wrong request type.');
        }

    }

    /**
     * Prepare error response xml
     * @param \DOMDocument $dom
     * @param $message
     * @return string
     */
    private function prepareErrorResponse(string $xml, $message):string
    {
        $encoded = $this->encoder->decode($xml, 'xml');

        $request = new Nack();
        $request->setErrorMessage($message);

        list($response, $rootNodeName) = $request->process($encoded);

        $responseXml = $this->encoder->encode($response, 'xml', ['xml_root_node_name' => $rootNodeName, 'xml_encoding' => 'UTF-8']);

        //validate xml
        if (!$request->validateResponseXml($responseXml)) {
            throw new \Exception('The response contains an error structure.');
        }

        return $responseXml;
    }

}