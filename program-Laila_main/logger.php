<?php

function logUserAction($userId, $action, $resource, $result)
{
  $timestamp = date('Y-m-d H:i:s');

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "prosecutor_office";


  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $stmt = $conn->prepare("INSERT INTO logs (user_id, action, resource, result, timestamp) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $userId, $action, $resource, $result, $timestamp);


  $stmt->close();
  $conn->close();
}
