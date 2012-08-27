<?php

namespace Jazzyweb\doDocBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/docs/easybook-doc-en');

        //$this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
        print_r($client->getResponse()->getContent());
    }
}
