<?php

declare(strict_types=1);

namespace MedMij\OpenPGO\Whitelist;

use GuzzleHttp\Client as HttpClient;
use JMS\Serializer\SerializerBuilder;
use MedMij\OpenPGO\Client\MedMijClient;

class WhitelistClient extends MedMijClient
{
    /**
     * @param HttpClient $httpClient
     * @param string     $endpoint
     */
    public function __construct(HttpClient $httpClient, string $endpoint)
    {
        parent::__construct($httpClient, $endpoint, file_get_contents(__DIR__.'/../../Resources/xsd/MedMij_Whitelist.xsd'));
    }

    /**
     * @return Whitelist
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWhitelist(): Whitelist
    {
        $response = $this->httpClient->request('GET', $this->endpoint);
        $this->assertXmlContentType($response);

        $document = new \DOMDocument();
        $xml = $response->getBody()->getContents();
        $document->loadXML($xml);
        $this->assertValidSchema($document);

        /** @var Serializer $serializer */
        $serializer = SerializerBuilder::create()->build();

        return $serializer->deserialize($xml, Whitelist::class, 'xml');
    }
}