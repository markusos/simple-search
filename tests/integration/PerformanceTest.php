<?php namespace Search;


class PerformanceTest extends \PHPUnit_Framework_TestCase {


    public function setUp()
    {
        // Set up engine and index data
        $engine = new Engine();
        $engine->clear();

        $file = 'tests/Wikipedia_sample_dataset.json';
        $data = json_decode(file_get_contents($file))->data;

        foreach ($data as $article) {
            $engine->addDocument(new Document($article->title, $article->content, ''));
        }
    }

    public function tearDown()
    {
        // Set up engine and index data
        $engine = new Engine();
        $engine->clear();
    }

    public function testPerformance() {

        $testQueries = [
            "Test",
            "This is a test query",
            "Computer architecture",
            "A physics theory",
            "漢語水平考試",
            "New York, Stockholm, Paris, Berlin, London",
            "String theory is a set of attempts to model the four, known fundamental interactions",
            "In French, the title of the movie is Le Voyage dans la lune",
            "And any are aren't as at be because been before being below between both but by can't",
            "One, Two, Three, Four, Five"
        ];

        $start = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            foreach ($testQueries as $query) {
                $results = (new Engine())->search($query);
            }
        }

        $time_elapsed_us = microtime(true) - $start;

        echo PHP_EOL . "Performance test: " . $time_elapsed_us . "s" . PHP_EOL;
    }

}