<?php
$host = "ep-falling-night-achtxr8g-pooler.sa-east-1.aws.neon.tech";
$db   = "neondb";
$user = "neondb_owner";
$pass = "npg_5vfQidY9lJCN";
$port = 5432;

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

$conn = new PDO($dsn, $user, $pass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
