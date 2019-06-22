<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Get single user
$app->get('/api/users/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM users WHERE id = $id";

  try {
    // Get DB object
    $db = getDB();
    $userData = '';
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    echo json_encode($users);
  } catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
});


//  Get all users

$app->get('/api/users', function (Request $request, Response $response) {

  $sql = "SELECT * FROM users ORDER BY date_joined desc";

  try {
    // Get DB object
    $db = getDB();
    $userData = '';
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    if ($users === false) {
      echo '{"error": {"text": "User does not exist"}}';
    } else {
      echo json_encode($users);
    }
  } catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
});

// Get single user
$app->get('/api/users/user/{userId}', function (Request $request, Response $response) {
  $userId = $request->getAttribute('userId');

  $sql = "SELECT * FROM users WHERE id = $userId";

  try {
    // Get DB object
    $db = getDB();
    $userData = '';
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $user = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    echo json_encode($user);
  } catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
});

// Update user
$app->put('/api/users/update/{userId}', function (Request $request, Response $response) {
  $id = $request->getAttribute('userId');
  $username = $request->getParam('username');
  $email = $request->getParam('email');
  $name = $request->getParam('name');
  $role = $request->getParam('role');
  $isSuspended = $request->getParam('isSuspended');

  $sql_insert = "UPDATE users 
  SET 
  username = :username,
  name = :name,
  email = :email,
  role = :role,
  isSuspended = :isSuspended,
  isDeleted = :isDeleted
  WHERE id = $id";

  try {
    // $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
    // $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);

    // if (strlen(trim($username)) > 0 && strlen(trim($email)) > 0 && $email_check > 0 && $username_check > 0) {
    //   $db = getDB();
    //   $userData = '';
    //   $sql = "SELECT id FROM users WHERE username=:username or email=:email";
    //   $stmt = $db->prepare($sql);
    //   $stmt->bindParam("username", $username);
    //   $stmt->bindParam("email", $email);
    //   $stmt->execute();
    //   $mainCount = $stmt->rowCount();

    // if ($mainCount == 0) {
    // Insert user values
    $db = getDB();
    $userData = '';
    $stmt_insert = $db->prepare($sql_insert);
    $stmt_insert->bindParam("username", $username);
    $stmt_insert->bindParam("email", $email);
    $stmt_insert->bindParam("name", $name);
    $stmt_insert->bindParam("role", $role);
    $stmt_insert->bindParam("isSuspended", $isSuspended);
    $stmt_insert->bindParam("isDeleted", $isDeleted);
    $stmt_insert->execute();

    $userData = internalUserDetails($email);

    $db = null;
    echo '{
        "userData":' . json_encode($userData) . '
      }';
  } catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
});
// $app->run();
