<?php
error_reporting(1);
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


// Request Service
$app->post('/api/requests', function(Request $request, Response $response){

  $user_id            = $request->getParam('user_id');
  $user_name          = $request->getParam('user_name');
  $service_type       = $request->getParam('service_type');
  $service_priority   = $request->getParam('service_priority');
  $service_desc       = $request->getParam('service_desc');
  $start_date         = $request->getParam('start_date');
  $end_date           = $request->getParam('end_date');
  $tel_no             = $request->getParam('tel_no');
  $house_no           = $request->getParam('house_no');
  $house_area         = $request->getParam('house_area');
  $house_landmark     = $request->getParam('house_landmark');
  $digital_address    = $request->getParam('digital_address');
  $city               = $request->getParam('city');
  $region             = $request->getParam('region');
  $status             = $request->getParam('status');
  $date_added         = date('Y-m-d H:i:s');

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
        $sql_insert = "INSERT INTO requests 
        (
          user_id,
          user_name,
          service_type,
          service_priority,
          service_desc,
          start_date,
          end_date,
          tel_no,
          house_no,
          house_area,
          house_landmark,
          digital_address,
          city,
          region,
          status,
          date_added

        ) 
        VALUE 
        (
          :user_id,
          :user_name,
          :service_type,
          :service_priority,
          :service_desc,
          :start_date,
          :end_date,
          :tel_no,
          :house_no,
          :house_area,
          :house_landmark,
          :digital_address,
          :city,
          :region,
          :status,
          :date_added
        )";
        $stmt_insert = $db->prepare($sql_insert);
        $stmt_insert->bindParam("user_id", $user_id);
        $stmt_insert->bindParam("user_name", $user_name);
        $stmt_insert->bindParam("service_type", $service_type);
        $stmt_insert->bindParam("service_priority", $service_priority);
        $stmt_insert->bindParam("service_desc", $service_desc);
        $stmt_insert->bindParam("start_date", $start_date);
        $stmt_insert->bindParam("end_date", $end_date);
        $stmt_insert->bindParam("tel_no", $tel_no);
        $stmt_insert->bindParam("house_no", $house_no);
        $stmt_insert->bindParam("house_area", $house_area);
        $stmt_insert->bindParam("house_landmark", $house_landmark);
        $stmt_insert->bindParam("digital_address", $digital_address);
        $stmt_insert->bindParam("city", $city);
        $stmt_insert->bindParam("region", $region);
        $stmt_insert->bindParam("status", $status);
        $stmt_insert->bindParam("date_added", $date_added);
        $stmt_insert->execute();

        $requestData = requestDetails($user_id);
      // }

      $db = null;

      echo '{
        "requestData":'.json_encode($requestData).'
      }';
    // }

  } catch (PDOException $e){
    echo '{
      "error":{
        "text":' .$e->getMessage(). '
      }
    }';
  }
});

// Get all requests by a  user
$app->get('/api/requests/{id}', function(Request $request, Response $response){
  $id = $request->getAttribute('id');

  $sql = "SELECT * FROM requests WHERE user_id = $id ORDER BY date_added desc";

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

// Get single request by a  user
$app->get('/api/requests/user/{requestId}', function(Request $request, Response $response){
  $requestId = $request->getAttribute('requestId');

  $sql = "SELECT * FROM requests WHERE id = $requestId";

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

// Get single request by a  user
$app->get('/api/requests/bill/{requestId}', function(Request $request, Response $response){
  $requestId = $request->getAttribute('requestId');

  $sql = "SELECT * FROM bills WHERE request_id = $requestId";

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


// Get all requests by a  user
$app->get('/api/requests/admin/{adminId}', function(Request $request, Response $response){
  $adminId = $request->getAttribute('adminId');

  $sql = "SELECT * FROM requests ORDER BY date_added desc";

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

// Update request to started
$app->put('/api/requests/update/start/{id}', function(Request $request, Response $response){
  $id = $request->getAttribute('id');
  $admin_started_date = date('Y-m-d H:i:s');
  $served_by = $request->getParam('served_by');
  $status = $request->getParam('status');

  $sql = "UPDATE requests 
  SET 
  admin_started_at = :admin_started_date,
  served_by = :served_by,
  status = :status
  WHERE id = $id";

  try {
    // Get DB object
      $db = getDB();
      $requestData = '';
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':admin_started_date', $admin_started_date);
      $stmt->bindParam(':served_by', $served_by);
      $stmt->bindParam(':status', $status);
      $stmt->execute();

      $requests = $stmt->fetchAll(PDO::FETCH_OBJ);

      $db = null;

      echo json_encode($requests);

  }catch(PDOException $e) {
    echo '{"error": {"text": '.$e->getMessage().'}}';
  }
});

// Update request to ended
$app->put('/api/requests/update/end/{id}', function(Request $request, Response $response){
  $id = $request->getAttribute('id');
  $admin_ended_date = date('Y-m-d H:i:s');
  $status = $request->getParam('status');

  $sql = "UPDATE requests 
  SET 
  admin_ended_at = :admin_ended_date,
  status = :status
  WHERE id = $id";

  try {
    // Get DB object
      $db = getDB();
      $requestData = '';
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':admin_ended_date', $admin_ended_date);
      $stmt->bindParam(':status', $status);
      $stmt->execute();

      $requests = $stmt->fetchAll(PDO::FETCH_OBJ);

      $db = null;

      echo json_encode($requests);

  }catch(PDOException $e) {
    echo '{"error": {"text": '.$e->getMessage().'}}';
  }
});


//  Inter details based on user
function requestDetails($input){
  $sql = "SELECT * FROM requests WHERE user_id =:input";
  try{
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("input", $input);
    $stmt->execute();
    $requestDetails = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $requestDetails;
  } catch (PDOException $e){
    echo '{
      "error":{
        "text":' .$e->getMessage(). '
      }
    }';
  }
}


?>