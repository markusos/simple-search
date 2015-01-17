<?php namespace Search;


class EngineTest extends \PHPUnit_Framework_TestCase {

    public function testEngine() {

        $engine = new Engine();

        $docs = [
            new Document('A', 'a d f', '/a/a'),
            new Document('B', 'b d d', '/b/b'),
            new Document('C', 'c e f', '/c/c'),
            new Document('D', 'd d d', '/c/c'),
            new Document('E', 'e e f', '/c/c'),
            new Document('F', 'f a b', '/c/c'),
        ];

        foreach ($docs as $doc) {
            $engine->addDocument($doc);
        }

        $results = $engine->search('d');

        // Validate the result content
        $this->assertEquals(3, count($results));
        $this->assertContains($docs[0], $results);
        $this->assertContains($docs[1], $results);
        $this->assertNotContains($docs[2], $results);
        $this->assertContains($docs[3], $results);

        // Validate the result order
        $this->assertEquals($docs[3], $results[0]);
        $this->assertEquals($docs[1], $results[1]);
        $this->assertEquals($docs[0], $results[2]);
    }
}
