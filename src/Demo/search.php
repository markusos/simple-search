<?php

require "setup.php";

$query = filter_input(INPUT_GET, "query", FILTER_SANITIZE_SPECIAL_CHARS);

if (isset($query)) {
    echo json_encode($engine->search($query));
}