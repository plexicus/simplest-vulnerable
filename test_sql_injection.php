<?php
// Simulate a GET request to index.php with a specific id
$response = file_get_contents('http://localhost/index.php?id=1');
echo $response;
?>
