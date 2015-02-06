<?php namespace Search;

class PersistentEngineTest extends \PHPUnit_Framework_TestCase{

    public function testFindKeywords() {
        $engine = new Engine();

        $file = 'tests/Wikipedia_sample_dataset.json';
        $dataset = json_decode(file_get_contents($file));

        foreach ($dataset->data as $article) {
            $engine->addDocument(new Document($article->topic, $article->text, ''));
        }
        $results = $engine->findKeywords('In computer engineering, computer architecture is the conceptual design and fundamental operational structure of a computer system.');
        $this->assertEquals('computer', $results[0]['keyword']);

        $this->assertEquals(count($dataset->data), $engine->size());
        $engine->clear();
        $this->assertEquals(0, $engine->size());
    }

}

