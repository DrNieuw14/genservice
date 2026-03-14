<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "genservis_db";

$conn = new mysqli("localhost", "root", "", "genservis_db", 3307);

if(!$conn){
die("Database connection failed");
}


?>