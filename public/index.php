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

function addEmployee($request) {
   
   /* $request = \Slim\Slim::getInstance()->request();
  	parse_str($request->getBody(),$update); 
  	print_r($request->getBody());
    
    $sql = "INSERT INTO `library`(`book_id`, `book_name`, `book_isbn`)  VALUES (:id, :name, :isbn)";
    try {

    	//Validate your filed before insert in db
            if(!is_array($update) || (!$update)) {
                throw new Exception('Invalid data received');   
            }
            // put your require filed list here
            if((!$update['id']) || (!$update['name']) || (!$update['isbn'])){
                throw new Exception('Missing values for require fields');
            }


        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $update['id']);
        $stmt->bindParam("name", $update['name']);
        $stmt->bindParam("isbn", $update['isbn']);
        $stmt->execute();
       // $update->id = $db->lastInsertId();
        $db = null;
        echo "Shofol";
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    catch(Exception $e) {
            //error_log($e->getMessage(), 3, '/var/tmp/php.log');
            echo '{"error":{"text":'. $e->getMessage() .'}}'; 
        }*/

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





/*
$app->get('/books', function() {

 require_once('db.php');

 $query = "select * from library order by book_id";

 $result = $conn->query($query);

 // var_dump($result);

 while ($row = $result->fetch_assoc()){

$data[] = $row;

 }

 echo json_encode($data);

});*/










// Run app
$app->run();
