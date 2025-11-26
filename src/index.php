<?php
include_once('common.php');

$result = $conn->query("Show TABLES;");
echo "<pre>";
while ($row = $result->fetch_array()){
    print_r($row);
}
echo "</pre>";
$conn->close();

?>