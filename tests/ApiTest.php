<?php

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use App\Kernel;
use App\Service\XmlService;
use App\Requests\Ping;
use App\Requests\Reverse;

class ApiTest extends WebTestCase
{
    private $encoder;

    /**
     * Setup object for testing
     */
    protected function setUp()
    {
        $this->encoder = new XmlEncoder();
        $serializer = new Serializer(array(new CustomNormalizer()), array('xml' => new XmlEncoder()));
        $this->encoder->setSerializer($serializer);
    }

    protected function tearDown()
    {
        $this->encoder = null;
    }

    public function testRequestXmlValidation()
    {
        $xml = file_get_contents(dirname(__DIR__) . '/src/Resources/samples/ping_request.xml');
        $class = new Ping();
        $this->assertTrue($class->validateRequestXml($xml));

        $xml2 = file_get_contents(dirname(__DIR__) . '/src/Resources/samples/reverse_request.xml');
        $class2 = new Reverse();
        $this->assertTrue($class2->validateRequestXml($xml2));
    }

    public function testService()
    {
        $xml = file_get_contents(dirname(__DIR__) . '/src/Resources/samples/reverse_request.xml');

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;
        $service = $container->get(XmlService::class);

        $responseXml = $service->processing($xml);
        $encoded = $this->encoder->decode($responseXml, 'xml');


        $xml2 = file_get_contents(dirname(__DIR__) . '/src/Resources/samples/reverse_response.xml');
        $encoded2 = $this->encoder->decode($xml2, 'xml');
        $this->assertSame($encoded['body']['reverse'], $encoded2['body']['reverse']);


    }
}