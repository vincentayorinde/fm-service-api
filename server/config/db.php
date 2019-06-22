<?php

error_reporting(1);
session_start();

//  DATABASE COFIGURATION

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'fmservices');
define('BASE_URL', 'http://localhost/fm-services-api/server');
define('SITE_KEY', 'secretKey');


function getDB(){
  $dbhost = DB_SERVER;
  $dbuser = DB_USERNAME;
  $dbpass = DB_PASSWORD;
  $dbname = DB_DATABASE;
  $dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
  $dbConnection->exec("set name utf8");
  $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $dbConnection;
}

// DATABASE CONFIGURATION END

// API key encryption 

function apiKey($session_uid){
  $key = md5(SITE_KEY.$session_uid);
  return hash('sha256', $key);
}