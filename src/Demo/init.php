<?php

require "setup.php";

// Create the table if it does not exist
try{
    $query = "CREATE TABLE documents (id int NOT NULL UNIQUE, title VARCHAR (255) NOT NULL, content VARCHAR (2048) NOT NULL);";
    $statement = $pdo->prepare($query);
    $statement->execute();
}
catch (\Exception $e) {
    // Do nothing!
}

// If index is empty, add test data set.
if ($index->size() === 0) {
    $index = $store->buildIndex($index);
}

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

