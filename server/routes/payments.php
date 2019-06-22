<?php
error_reporting(1);
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


// Create Bill
$app->post('/api/payments', function (Request $request, Response $response) {

  $bill_id       = $request->getParam('id');
  $user_id       = $request->getParam('user_id');
  $user_name     = $request->getParam('user_name');
  $service_type  = $request->getParam('service_type');
  $amount        = $request->getParam('amount');
  // $payment_method= $request->getParam('payment_method');
  $approval      = 'Not Confirmed Yet';
  $paid_at       = date('Y-m-d H:i:s');

  try {
    // $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
    // $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
    // $password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);

    // if(strlen(trim($username)) > 0 && strlen(trim($password)) > 0 && strlen(trim($email)) > 0 && $email_check > 0 && $username_check > 0 && $password_check > 0) {
    $db = getDB();
    $userData = '';
    // $sql = "SELECT id FROM users WHERE username=:username or email=:email";
    // $stmt = $db->prepare($sql);
    // $stmt->bindParam("username", $username);
    // $stmt->bindParam("email", $email);
    // $stmt->execute();
    // $mainCount = $stmt->rowCount();
    // $created = time();

    // if($mainCount == 0){
    // Insert user values
    $sql_insert = "INSERT INTO payments 
        (
          bill_id,
          user_id,
          user_name,
          service_type,
          amount,
          payment_method,
          approval,
          paid_at
        ) 
        VALUE 
        (
          :bill_id,
          :user_id,
          :user_name,
          :service_type,
          :amount,
          :payment_method,
          :approval,
          :paid_at
        )";
    $stmt_insert = $db->prepare($sql_insert);
    $stmt_insert->bindParam("bill_id", $bill_id);
    $stmt_insert->bindParam("user_id", $user_id);
    $stmt_insert->bindParam("user_name", $user_name);
    $stmt_insert->bindParam("service_type", $service_type);
    $stmt_insert->bindParam("amount", $amount);
    $stmt_insert->bindParam("payment_method", $payment_method);
    $stmt_insert->bindParam("approval", $approval);
    $stmt_insert->bindParam("paid_at", $paid_at);
    $stmt_insert->execute();

    // }
    if ($stmt_insert) {
      $request_update = "UPDATE bills 
        SET paid_at = :paid_at, status = 'Paid' WHERE id = :bill_id";

      $stmt_update = $db->prepare($request_update);
      $stmt_update->bindParam("paid_at", $paid_at);
      $stmt_update->bindParam("bill_id", $bill_id);
      $stmt_update->execute();
    }

    $db = null;

    echo '{"paymentData": "Payment Issued"}';
    // }

  } catch (PDOException $e) {
    echo '{
      "error":{
        "text":' . $e->getMessage() . '
      }
    }';
  }
});


// Get all payments by a  user
$app->get('/api/payments/{id}', function (Request $request, Response $response) {
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM payments WHERE user_id = $id ORDER BY paid_at desc";

  try {
    // Get DB object
    $db = getDB();
    $requestData = '';
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $requests = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    echo json_encode($requests);
  } catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
});

// Get single payment by a  user
$app->get('/api/payments/user/{paymentId}', function (Request $request, Response $response) {
  $paymentId = $request->getAttribute('paymentId');

  $sql = "SELECT * FROM payments WHERE id = $paymentId";

  try {
    // Get DB object
    $db = getDB();
    $requestData = '';
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $request = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    echo json_encode($request);
  } catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
});


// Get all payments for admin
$app->get('/api/payments', function (Request $request, Response $response) {

  $sql = "SELECT * FROM payments ORDER BY paid_at desc";

  try {
    // Get DB object
    $db = getDB();
    $requestData = '';
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $requests = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;

    echo json_encode($requests);
  } catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
});
// Update payment as confirmed
$app->put('/api/payments/update/{paymentId}', function (Request $request, Response $response) {
  $id = $request->getAttribute('paymentId');
  $approval = $request->getParam('approval');
  $confirmed_at = date('Y-m-d H:i:s');

  $sql = "UPDATE payments 
  SET 
  approval = :approval,
  confirmed_at = :confirmed_at
  WHERE id = $id";

  try {
    // Get DB object
    $db = getDB();
    $requestData = '';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':approval', $approval);
    $stmt->bindParam(':confirmed_at', $confirmed_at);
    $stmt->execute();

    // echo $payment = $stmt->fetchAll(PDO::FETCH_OBJ);

    // $db = null;

    // echo json_encode($payment);
  } catch (PDOException $e) {
    echo '{"error": {"text": ' . $e->getMessage() . '}}';
  }
});
