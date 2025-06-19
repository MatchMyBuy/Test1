<?php

$conn = mysqli_connect("localhost", "root", "", "log_in");

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
