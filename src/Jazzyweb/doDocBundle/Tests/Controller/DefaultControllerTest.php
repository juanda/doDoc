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

    /**
     * 1) Request for all documents of a book which doesn't exist
     */
    public function testGetAllDocumentsInNonExistentBook() {
        
        $crawler = $this->client->request('GET', '/book/estenoexiste/docs');
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/does not exists/', $this->client->getResponse()->getContent());
    }

    /**
     * 2) Request for all documents of an existing book 
     */
    public function testGetAllDocumentInExistingBook() {
                
        $crawler = $this->client->request('GET', '/book/test-for-dodoc/docs');
        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/documents/', $this->client->getResponse()->getContent());
        $this->assertRegExp('/chapter1.md/', $this->client->getResponse()->getContent());
        $this->assertRegExp('/chapter2.md/', $this->client->getResponse()->getContent());
    }

    /**
     * 3) Request for a document in a book which doesn't exist
     */
    public function testGetDocumentInNonExistingBook() {
        
        $crawler = $this->client->request('GET', '/book/estenoexiste/docs/chapter1.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/does not exist/', $this->client->getResponse()->getContent());
    }

    /**
     * 4) Request for a document in an existing book
     */
    public function testGetDocumentInExistingBook() {

        $crawler = $this->client->request('GET', '/book/test-for-dodoc/docs/chapter2.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $file = $this->docDir . '/test-for-dodoc/' . $this->contentsDirname . '/chapter2.md';
        $content = file_get_contents($file);        
        $this->assertRegExp('/The title of the second chapter/', $content);
    }

    /**
     * 5) Request for a document which doesn' exist in a given book
     */
    public function testGetDocumentWhichDoesntExistInExistingBook() {

        $crawler = $this->client->request('GET', '/book/test-for-dodoc/docs/chapter112.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * 6) Create a new document in an existing book
     */
    public function testNewDocumentOK() {
        $crawler = $this->client->request('POST', '/book/test-for-dodoc/docs/chapter_new.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/chapter_new.md/', $this->client->getResponse()->getContent());

        // This is to make the test idempotent
        $finder = new \Symfony\Component\Filesystem\Filesystem();
        $finder->remove($this->docDir . '/test-for-dodoc/' . $this->contentsDirname . '/chapter_new.md');
    }

    /**
     * 7) Create a new document with some content
     */
    public function testNewDocumentWithContentOK() {
        $crawler = $this->client->request('POST', '/book/test-for-dodoc/docs/chapter_new.md', array(), array(), array(), json_encode(array('content' => 'El perro de san roque no tiene rabo')));

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/chapter_new.md/', $this->client->getResponse()->getContent());

        $file = $this->docDir . '/test-for-dodoc/' . $this->contentsDirname . '/chapter_new.md';

        $content = \file_get_contents($file);

        $this->assertRegExp('/El perro de san roque no tiene rabo/', $content);

        // This is to make the test idempotent
        $finder = new \Symfony\Component\Filesystem\Filesystem();
        $finder->remove($file);
    }

    /**
     * 8) Create a new document which name match up an existing one in the given book
     */
    public function testNewDocumentFailsFileExists() {
        $crawler = $this->client->request('POST', '/book/test-for-dodoc/docs/chapter1.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/There is a document/', $this->client->getResponse()->getContent());
    }

    /**
     * 9) Create a document in a non existing book
     */
    public function testNewDocumentFailsBookNotExist() {
        $crawler = $this->client->request('POST', '/book/estenoexiste/docs/chapter45.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/does not exists/', $this->client->getResponse()->getContent());
    }

    /**
     * 10) Update an existing document in a given book which also exists
     */
    public function testSaveDocumentOK() {
        $crawler = $this->client->request(
                'PUT', '/book/test-for-dodoc/docs/chapter1.md', array(), array(), array(), json_encode(array('content' => 'El perro de san roque no tiene rabo'))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/chapter1.md/', $this->client->getResponse()->getContent());
    }

    public function testSaveDocumentFailsFileDoesnExist() {
        $crawler = $this->client->request(
                'PUT', '/book/test-for-dodoc/docs/chapter1828.md', array(), array(), array(), json_encode(array('content' => 'El perro de san roque no tiene rabo'))
        );

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/There is not a document named chapter1828.md/', $this->client->getResponse()->getContent());
    }

    public function testDeleteDocumentOK() {

        // first of all let's create a new document to remove
        $file = $this->docDir . '/test-for-dodoc/' . $this->contentsDirname . '/chapter_new.md';
        \file_put_contents($file, "some text");

        $crawler = $this->client->request('DELETE', '/book/test-for-dodoc/docs/chapter_new.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/has been removed/', $this->client->getResponse()->getContent());
    }

    public function testDeleteDocumentInNoExistingBook() {
        $crawler = $this->client->request('DELETE', '/book/estenoexiste/docs/chapter_new.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/does not exists/', $this->client->getResponse()->getContent());
    }

    public function testDeleteDocumentWhichDoesntExist() {
        $crawler = $this->client->request('DELETE', '/book/test-for-dodoc/docs/chapter_new.md');

        $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp('/There is not a document/', $this->client->getResponse()->getContent());
    }

}
