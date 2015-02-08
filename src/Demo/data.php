<?php

require '../../vendor/autoload.php';

$engine = new Search\Engine();

// If index is empty, add test data set.
if ($engine->size() === 0) {
    $file = '../../tests/Wikipedia_sample_dataset.json';
    $data = json_decode(file_get_contents($file))->data;

    foreach ($data as $article) {
        $engine->addDocument(new Search\Document($article->title, $article->content, ''));
    }
}

if (isset($_GET["query"])) {
    $query =  htmlspecialchars($_GET["query"]);
    echo json_encode($engine->search($query));
}


