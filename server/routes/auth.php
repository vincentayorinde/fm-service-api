<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


// Create Account
$app->post('/api/auth/signup', function (Request $request, Response $response) {

  $email = $request->getParam('email');
  $name = $request->getParam('name');
  $username = $request->getParam('username');
  $password = $request->getParam('password');
  $role = $request->getParam('role');
  $data_joined = date('Y-m-d H:i:s');

  try {
    $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
    $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
    $password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);

    if (strlen(trim($username)) > 0 && strlen(trim($password)) > 0 && strlen(trim($email)) > 0 && $email_check > 0 && $username_check > 0 && $password_check > 0) {
      $db = getDB();
      $userData = '';
      $sql = "SELECT id FROM users WHERE username=:username or email=:email";
      $stmt = $db->prepare($sql);
      $stmt->bindParam("username", $username);
      $stmt->bindParam("email", $email);
      $stmt->execute();
      $mainCount = $stmt->rowCount();
      $created = time();

      if ($mainCount == 0) {
        // Insert user values
        $sql_insert = "INSERT INTO users (username, password, email, name, role, date_joined) VALUE (:username, :password, :email, :name, :role, :date_joined)";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bindParam("username", $username);
        $password = hash('sha256', $password);
        $stmt_insert->bindParam("password", $password);
        $stmt_insert->bindParam("email", $email);
        $stmt_insert->bindParam("name", $name);
        $stmt_insert->bindParam("role", $role);
        $stmt_insert->bindParam("date_joined", $data_joined);
        $stmt_insert->execute();

        $userData = internalUserDetails($email);
      }

      $db = null;

      echo '{
        "userData":' . json_encode($userData) . '
      }';
    }
  } catch (PDOException $e) {
    echo '{
      "error":{
        "text":' . $e->getMessage() . '
      }
    }';
  }
});

//  Login User
$app->post('/api/auth/login', function (Request $request, Response $response) {

  $username = $request->getParam('username');
  $password = $request->getParam('password');
  try {
    $db = getDB();
    $userData = '';
    $sql = "
    SELECT id, name, email, username, role, isSuspended 
    FROM users 
    WHERE (username=:username or email=:username) and password=:password";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $password = hash('sha256', $password);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $mainCount = $stmt->rowCount();
    $userData = $stmt->fetch(PDO::FETCH_OBJ);

    if (!empty($userData)) {
      $id = $userData->id;
      $userData->token = apiKey($id);
    }

    $db = null;
    echo '{
      "userData": ' . json_encode($userData) .
      '}';
  } catch (PDOException $e) {
    echo '{
      "error":{
        "text": ' . $e->getMessagge() .
      '}}';
  }
});

//  Inter details based on user
function internalUserDetails($input)
{
  $sql = "SELECT id, name, email, username, role FROM users WHERE username=:input or email=:input";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("input", $input);
    $stmt->execute();
    $usernameDetails = $stmt->fetch(PDO::FETCH_OBJ);
    $usernameDetails->token = apiKey($usernameDetails->id);
    return $usernameDetails;
  } catch (PDOException $e) {
    echo '{
      "error":{
        "text":' . $e->getMessage() . '
      }
    }';
  }
}
