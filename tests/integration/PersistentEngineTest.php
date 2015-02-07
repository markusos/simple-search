<?php namespace Search;

class PersistentEngineTest extends \PHPUnit_Framework_TestCase{

    /**
     * @var Engine
     */
    private $engine;

    private $data;

    public function setUp()
    {
        $this->engine = new Engine();
        $file = 'tests/Wikipedia_sample_dataset.json';
        $this->data = json_decode(file_get_contents($file))->data;

        foreach ($this->data as $article) {
            $this->engine->addDocument(new Document($article->title, $article->content, ''));
        }
    }

    public function tearDown()
    {
        $this->engine->clear();
        $this->assertEquals(0, $this->engine->size());
    }

    public function testSearch() {
        $results = $this->engine->search('computer architecture');
        $this->assertEquals('Computer architecture', $results[0]->title);

        $results = $this->engine->search('a physics theory');
        $this->assertEquals('String theory', $results[0]->title);
    }

    public function testFindKeywords() {
        $results = $this->engine->findKeywords('In computer engineering, computer architecture is the conceptual design and fundamental operational structure of a computer system.');
        $this->assertEquals('computer', $results[0]['keyword']);
    }

    public function testEngineSize() {
        $this->assertEquals(count($this->data), $this->engine->size());
    }


}

