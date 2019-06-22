<?php
error_reporting(1);
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


// Create Bill
$app->post('/api/bills', function(Request $request, Response $response){

  $service_type  = $request->getParam('service_type');
  $items         = json_encode($request->getParam('billItems'), true);
  $amount        = $request->getParam('amount');
  $due_date      = $request->getParam('due_date');
  $user_id       = $request->getParam('user_id');
  $user_name     = $request->getParam('user_name');
  $request_id    = $request->getParam('request_id');
  $status        = 'Not Paid';
  $generated_at  = date('Y-m-d H:i:s');
  $served_by     = $request->getParam('served_by');

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
        $sql_insert = "INSERT INTO bills 
        (
          service_type,
          items,
          generated_at,
          amount,
          due_date,
          user_id,
          user_name,
          request_id,
          served_by,
          status
        ) 
        VALUE 
        (
          :service_type,          
          :items,
          :generated_at,
          :amount,
          :due_date,
          :user_id,
          :user_name,
          :request_id,
          :served_by,
          :status
        )";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bindParam("service_type", $service_type);
        $stmt_insert->bindParam("items", $items);
        $stmt_insert->bindParam("generated_at", $generated_at);
        $stmt_insert->bindParam("amount", $amount);
        $stmt_insert->bindParam("due_date", $due_date);
        $stmt_insert->bindParam("user_id", $user_id);
        $stmt_insert->bindParam("user_name", $user_name);
        $stmt_insert->bindParam("request_id", $request_id);
        $stmt_insert->bindParam("served_by", $served_by);
        $stmt_insert->bindParam("status", $status);
        $stmt_insert->execute();

      // }
      if($stmt_insert){
        $request_update = "UPDATE requests 
        SET bill_generated_at = :generated_at WHERE id = :request_id";

        $stmt_update = $db->prepare($request_update);
        $stmt_update->bindParam("generated_at", $generated_at);
        $stmt_update->bindParam("request_id", $request_id);
        $stmt_update->execute();
      }

      $db = null;

      echo '{"billData": "Bill Created"}';
    // }

  } catch (PDOException $e){
    echo '{
      "error":{
        "text":' .$e->getMessage(). '
      }
    }';
  }
});


// Get all bills by a  user
$app->get('/api/bills/{id}', function(Request $request, Response $response){
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM bills WHERE user_id = $id ORDER BY generated_at desc";

  try {
    // Get DB object
      $db = getDB();
      $requestData = '';
      $stmt = $db->prepare($sql);
      $stmt->execute();

      $requests = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;

      echo json_encode($requests);

  }catch(PDOException $e) {
    echo '{"error": {"text": '.$e->getMessage().'}}';
  }
});

// Get single bill by a  user
$app->get('/api/bills/user/{billId}', function(Request $request, Response $response){
  $billId = $request->getAttribute('billId');

  $sql = "SELECT * FROM bills WHERE id = $billId";

  try {
    // Get DB object
      $db = getDB();
      $requestData = '';
      $stmt = $db->prepare($sql);
      $stmt->execute();

      $request = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;

      echo json_encode($request);

  }catch(PDOException $e) {
    echo '{"error": {"text": '.$e->getMessage().'}}';
  }
});

// Get all bills for admin
$app->get('/api/bills', function(Request $request, Response $response){
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM bills ORDER BY generated_at desc";

  try {
    // Get DB object
      $db = getDB();
      $requestData = '';
      $stmt = $db->prepare($sql);
      $stmt->execute();

      $requests = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;

      echo json_encode($requests);

  }catch(PDOException $e) {
    echo '{"error": {"text": '.$e->getMessage().'}}';
  }
});
