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

        $results = $engine->search('d a');

        // Validate the result content
        $this->assertEquals(4, count($results));
        $this->assertContains($docs[0], $results);
        $this->assertContains($docs[1], $results);
        $this->assertNotContains($docs[2], $results);
        $this->assertContains($docs[3], $results);
        $this->assertContains($docs[5], $results);

        // Validate the result order
        $this->assertEquals($docs[0], $results[0]);
        $this->assertEquals($docs[5], $results[1]);
    }

    public function testIndexData() {

        $engine = new Engine();

        $file = 'tests/Wikipedia_sample_dataset.json';
        $dataset = json_decode(file_get_contents($file));

        foreach ($dataset->data as $article) {
            $engine->addDocument(new Document($article->topic, $article->text, ''));
        }

        $results = $engine->search('computer architecture');

        $this->assertEquals('Computer architecture', $results[0]->getTitle());
    }

    public function testFindKeywords() {
        $engine = new Engine();

        $file = 'tests/Wikipedia_sample_dataset.json';
        $dataset = json_decode(file_get_contents($file));

        foreach ($dataset->data as $article) {
            $engine->addDocument(new Document($article->topic, $article->text, ''));
        }

        $results = $engine->findKeywords('In computer engineering, computer architecture is the conceptual design and fundamental operational structure of a computer system.');
        $this->assertEquals('computer', $results[0]['keyword']);

    }
}
