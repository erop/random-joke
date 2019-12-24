<?php

namespace App\Tests\Acceptance;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testMainFormIsBuilt(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $buttonCrawler = $crawler->selectButton('Send joke');
        $this->assertNotNull($buttonCrawler->form());
    }
}
