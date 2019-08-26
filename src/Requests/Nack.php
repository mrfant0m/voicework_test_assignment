<?php

namespace App\Requests;

/**
 * Class for ping requests processing
 */
class Nack extends XmlRequests
{
    const PATH_RESPONSE_XSD = '/Resources/xsds/nack.xsd';
    const RESPONSE_TYPE = 'nack';

    private $errorMessage;


    /**
     * Generate response
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function process(array $data):array
    {
        $date = new \DateTime();

        $result = [
            'header' => [
                'type' => self::RESPONSE_TYPE,
                'sender' => isset($data['header']['recipient']) ? $data['header']['recipient'] : '',
                'recipient' => isset($data['header']['sender']) ? $data['header']['sender'] : '',
                'reference' => isset($data['header']['reference']) ? $data['header']['reference'] : '',
                'timestamp' => $date->format('Y-m-d\TH:i:s.vP'),
            ],
            'body' => [
                'error' => [
                    'code' => '404',
                    'message' => $this->errorMessage
                ]
            ]
        ];

        return [$result, self::RESPONSE_TYPE];
    }

    public function setErrorMessage(string $message):void
    {
        $this->errorMessage = $message;
    }

}