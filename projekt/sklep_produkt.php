<?php
if (isset($_GET['id'])) {

$id = $_GET['id'];


echo "Product ID: " . $id;
} else {

echo "Invalid product ID";
}
?>