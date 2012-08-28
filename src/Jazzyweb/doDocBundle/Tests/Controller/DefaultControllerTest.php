<?php

namespace Jazzyweb\doDocBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    public function __construct() {
        parent::__construct();

        $this->client = static::createClient();

        $this->container = $this->client->getContainer();

        $this->docDir = $this->container->getParameter('jazzyweb.dodocmanager.docdir');
        $this->contentsDirname = $this->container->getParameter('jazzyweb.dodocmanager.contentsdirname');
        $this->outputDirname = $this->container->getParameter('jazzyweb.dodocmanager.outputdirname');
        $this->configFilename = $this->container->getParameter('jazzyweb.dodocmanager.configfilename');
    }

    public function testGetAllDocumentsInNonExistentBook() {


// requesting all the documents of a non existent book
        $crawler = $this->client->request('GET', '/book/estenoexiste/docs');
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/does not exists/', $this->client->getResponse()->getContent());
    }

    public function testGetAllDocumentInExistingBook() {
// requesting all the documents of an existent book
        $crawler = $this->client->request('GET', '/book/test-for-dodoc/docs');
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/documents/', $this->client->getResponse()->getContent());
        $this->assertRegExp('/chapter1.md/', $this->client->getResponse()->getContent());
        $this->assertRegExp('/chapter2.md/', $this->client->getResponse()->getContent());
    }

    public function testGetDocumentInNonExistingBook() {

// requesting all the documents of a non existent book
        $crawler = $this->client->request('GET', '/book/estenoexiste/docs/chapter1.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/does not exist/', $this->client->getResponse()->getContent());
    }

    public function testGetDocumentInExistingBook() {

// requesting all the documents of a non existent book
        $crawler = $this->client->request('GET', '/book/test-for-dodoc/docs/chapter1.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testGetDocumentWhichNonExitsInExistingBook() {
// requesting all the documents of a non existent book
        $crawler = $this->client->request('GET', '/book/test-for-dodoc/docs/chapter112.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testNewDocumentOK() {
        $crawler = $this->client->request('POST', '/book/test-for-dodoc/docs/chapter_new.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/chapter_new.md/', $this->client->getResponse()->getContent());

        // This is to make the test idempotent
        $finder = new \Symfony\Component\Filesystem\Filesystem();
        $finder->remove($this->docDir . '/test-for-dodoc/' . $this->contentsDirname . '/chapter_new.md');
    }

    public function testNewDocumentFailsFileExists() {
        $crawler = $this->client->request('POST', '/book/test-for-dodoc/docs/chapter1.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/There is a document/', $this->client->getResponse()->getContent());
    }

    public function testNewDocumentFailsBookNotExist() {
        $crawler = $this->client->request('POST', '/book/estenoexiste/docs/chapter1.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/does not exists/', $this->client->getResponse()->getContent());
    }

    public function testSaveDocumentOK() {
        $crawler = $this->client->request(
                'PUT',
                '/book/test-for-dodoc/docs/chapter1.md',
                array(),
                array(),
                array(),
                json_encode(array('content' => 'El perro de san roque no tiene rabo'))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/chapter1.md/', $this->client->getResponse()->getContent());
        
    }

}
