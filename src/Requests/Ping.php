<?php

namespace App\Requests;

/**
 * Class for ping requests processing
 */
class Ping extends XmlRequests
{
    const PATH_REQUEST_XSD = '/Resources/xsds/ping_request.xsd';
    const PATH_RESPONSE_XSD = '/Resources/xsds/ping_response.xsd';
    const RESPONSE_TYPE = 'ping_response';

    /**
     * Generate response
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function process(array $data):array
    {
        $date = new \DateTime();
        $body = $data['body'];

        $result = [
            'header' => [
                'type' => self::RESPONSE_TYPE,
                'sender' => isset($data['header']['recipient']) ? $data['header']['recipient'] : '',
                'recipient' => isset($data['header']['sender']) ? $data['header']['sender'] : '',
                'reference' => isset($data['header']['reference']) ? $data['header']['reference'] : '',
                'timestamp' => $date->format('Y-m-d\TH:i:s.vP'),
            ],
            'body' => $body
        ];

        return [$result, self::RESPONSE_TYPE];
    }

}