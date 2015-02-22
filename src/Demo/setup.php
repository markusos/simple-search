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

    echo json_encode(true);
}
else {
    echo json_encode(false);
}


