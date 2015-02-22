<?php

require '../../vendor/autoload.php';

$engine = new Search\Engine();

if (isset($_GET["query"])) {
    $query =  htmlspecialchars($_GET["query"]);
    echo json_encode($engine->search($query));
}