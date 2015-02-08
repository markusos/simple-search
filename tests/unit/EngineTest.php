<?php namespace Search;


class EngineTest extends \PHPUnit_Framework_TestCase {

    public function testEngine() {

        $engine = new Engine(false);

        $docs = [
            new Document('A', 'aa dd ff', ''),
            new Document('B', 'bb dd dd', ''),
            new Document('C', 'cc ee ff', ''),
            new Document('D', 'dd dd dd', ''),
            new Document('E', 'ee ee ff', ''),
            new Document('F', 'ff aa bb', ''),
        ];

        foreach ($docs as $doc) {
            $engine->addDocument($doc);
        }

        $results = $engine->search('dd');

        // Validate the result content
        $this->assertEquals(3, count($results));
        $this->assertContains($docs[0], $results);
        $this->assertContains($docs[1], $results);
        $this->assertNotContains($docs[2], $results);
        $this->assertContains($docs[3], $results);

        $results = $engine->search('dd aa');

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

        $engine = new Engine(false);

        $file = 'tests/Wikipedia_sample_dataset.json';
        $dataset = json_decode(file_get_contents($file));

        foreach ($dataset->data as $article) {
            $engine->addDocument(new Document($article->title, $article->content, ''));
        }

        $results = $engine->search('computer architecture');
        $this->assertEquals('Computer architecture', $results[0]->title);

        $results = $engine->search('qwerty');
        $this->assertEquals([], $results);
    }

    public function testFindKeywords() {
        $engine = new Engine(false);

        $file = 'tests/Wikipedia_sample_dataset.json';
        $dataset = json_decode(file_get_contents($file));

        foreach ($dataset->data as $article) {
            $engine->addDocument(new Document($article->title, $article->content, ''));
        }

        $results = $engine->findKeywords('In computer engineering, computer architecture is the conceptual design and fundamental operational structure of a computer system.');
        $this->assertEquals('computer', $results[0]['keyword']);

        $this->assertEquals(count($dataset->data), $engine->size());
        $engine->clear();
        $this->assertEquals(0, $engine->size());
    }
}
