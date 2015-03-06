<?php namespace Search;


use Search\Config\Config;
use Search\Config\DefaultConfig;

class EngineTest extends \PHPUnit_Framework_TestCase {

    public function testEngine() {

        $engine = new Engine(Config::createBuilder()->testConfig()->stopWords([])->build());

        $docs = [
            new Document('A', 'a d f', ''),
            new Document('B', 'b d d', ''),
            new Document('C', 'c e f', ''),
            new Document('D', 'd d d', ''),
            new Document('E', 'e e f', ''),
            new Document('F', 'f a b', ''),
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
        $this->assertEquals($docs[3], $results[1]);
    }

    public function testIndexData() {

        $engine = new Engine(Config::createBuilder()->testConfig()->build());

        $file = 'tests/Wikipedia_sample_dataset.json';
        $dataset = json_decode(file_get_contents($file));

        foreach ($dataset->data as $article) {
            $engine->addDocument(new Document($article->title, $article->content, ''));
        }

        $results = $engine->search('computer architecture');
        $this->assertEquals('Computer architecture', $results[0]->title);

        $results = $engine->search('漢語水平考試');
        $this->assertEquals('Hanyu Shuiping Kaoshi', $results[0]->title);

        $results = $engine->search('qwerty');
        $this->assertEquals([], $results);

        $this->assertEquals([], $engine->search("And any are aren't as at be because been before being below between both but by can't"));
    }

    public function testFindKeywords() {
        $engine = new Engine(Config::createBuilder()->testConfig()->build());

        $file = 'tests/Wikipedia_sample_dataset.json';
        $dataset = json_decode(file_get_contents($file));

        foreach ($dataset->data as $article) {
            $engine->addDocument(new Document($article->title, $article->content, ''));
        }

        $results = $engine->findKeywords('In computer engineering, computer architecture is the conceptual design and fundamental operational structure of a computer system.');
        $this->assertEquals('computer', $results[0]['keyword']);

        $this->assertEquals(count($dataset->data), $engine->size());

        $engine->clear('index');
        $this->assertNotEquals(0, $engine->size());
        $engine->clear('store');
        $this->assertEquals(0, $engine->size());
    }
}
