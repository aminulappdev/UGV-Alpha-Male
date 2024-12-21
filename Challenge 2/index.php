<?php
$index = true;

require_once("./functions.php");

if (isset($_GET['page']) && $page = explode("/", $_GET['page'])) {
    if ($page[0] == 'json') {
        require_once("./json.php");
    } else {
        header("HTTP/1.0 404");
        exit;
    }
} else {
    header("HTTP/1.0 403");
    exit;
}
