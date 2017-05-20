<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';



function getConnection() {
    $dbhost="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="coolapi";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

// ---------------- get all emp -------------

function getEmployes($response) {
    $sql = "select * FROM library";
    try {
        $stmt = getConnection()->query($sql);
        $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        return json_encode($wines);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


// ------------------ get a single app ------------------

function getEmployee($request){

 $get_id = $request->getAttribute('id');

 //print_r($get_id);

 $sql = "select * FROM library WHERE book_id = '$get_id'";

  try {
        $stmt = getConnection()->query($sql);
        $wines = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        return json_encode($wines);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}


// --------------------- adding emp ----------------------------

function addEmployee($request) {
   

   $emp = json_decode($request->getBody());
    
    $sql = "INSERT INTO library (book_id, book_name, book_isbn) VALUES (:id, :name, :isbn)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $emp->id);
        $stmt->bindParam("name", $emp->name);
        $stmt->bindParam("isbn", $emp->isbn);
        $stmt->execute();
        $emp->id = $db->lastInsertId();
        $db = null;
        echo json_encode($emp);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

}


function updateEmployee($request) {
    $emp = json_decode($request->getBody());
    $id = $request->getAttribute('id');
    $sql = "UPDATE library SET book_name=:name, book_isbn=:isbn, book_category=:bcat WHERE book_id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $emp->name);
        $stmt->bindParam("isbn", $emp->isbn);
        $stmt->bindParam("bcat", $emp->bcat);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($emp);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


function deleteEmployee($request) {
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM library WHERE book_id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo '{"error":{"text":"Successfully! deleted Records"}}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}









// Run app
$app->run();
