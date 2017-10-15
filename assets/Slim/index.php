<?php
    require 'Slim/Slim.php';
    \Slim\Slim::registerAutoloader();
    error_reporting(-1);
	ini_set('display_errors', 'On');
    $app = new \Slim\Slim();
    // GET route
    // --------------------
    // ---------------- DELETE -------------------------
    $app->delete('/delete/user/:id', 'deleteUser');
    $app->delete('/delete/item/:id','deleteItem');
    $app->delete("/delete/category/:id","deleteCategory");
    $app->delete("/delete/house/:id","deleteHouse");
    // ---------------- add -------------------------
    $app->post('/add/house','addHouse');
    $app->post('/add/item','addItem');
    $app->post('/add/user','addUser');
    $app->post("/modify/user",'modifyUser');

    $app->post('/login','login');
   	
    $app->get('/get/item/:houseID','getItemsFromHouse');

// --------------- settings -------------
	$hostname = 'localhost';
	$username = 'root';
	$password = 'root';
	$dbname = 'jakubrybickidb';

// -----------End of settings -----------

// 
// --------------------------------------- DELETE USER -----------------------------

function deleteHouse($id){
	try{
		$conn=getConnection();
		
		$stmt=$conn->prepare("DELETE FROM house WHERE house_ID=:houseID");
		$stmt->bindParam(':houseID', $houseID);
		
		$houseID=$id;

		$stmt->execute();

		echo "Done!";
	}catch(PDOException $e){
		echo "$e";
	}
}

function deleteUser($id){
	//global $hostname, $username, $password, $dbname;
	
	try{
		$conn=getConnection();
		
		$stmt=$conn->prepare("DELETE FROM user WHERE user_ID=:userID");
		$stmt->bindParam(':userID', $userID);
		
		$userID=$id;

		$stmt->execute();

		echo "Done!";
	}catch(PDOException $e){
		echo "$e";
	}
}

function deleteItem($id){
	try{
		$conn=getConnection();
		
		$stmt=$conn->prepare("DELETE FROM item WHERE item_ID=:itemID");
		$stmt->bindParam(':itemID', $itemID);
		
		$itemID=$id;

		$stmt->execute();

		echo json_encode('{"done":"Done!"}');
	}catch(PDOException $e){
		echo "$e";
	}
}

function deleteCategory($id){
	try{
		$conn=getConnection();
		
		$stmt=$conn->prepare("DELETE FROM category WHERE category_ID=:catID");
		$stmt->bindParam(':catID', $catID);
		
		$catID=$id;

		$stmt->execute();

		echo "Done!";
	}catch(PDOException $e){
		echo "$e";
	}
}



// -------------------------------MODIFY DETAILS------------------------------------
function modifyUser(){
	$request=\Slim\Slim::getInstance()->request();
	$q=json_decode($request->getBody());
	// set user details
	try{
		$user=$q->user;
		$pass=$q->pass;
		$vKey=getUserVKey($user);
		$pass=hashPass($pass, $vKey);
		$newPass=$q->newPass;
		$newPass=hashPass($newPass,$vKey);
	}catch(Exception $e){die("Cannot find user");}
	//validate user
	if(!loginUserPass($user, $pass)){
		die("cannot find user");
	}
	//change password
	try{
		$conn=getConnection();

		$stmt=$conn->prepare("UPDATE user SET password=:newPass where username=:user");
		$stmt->bindParam(":newPass",$newPass);
		$stmt->bindParam(":user",$user);
		$stmt->execute();
	}catch(PDOException $e){	die($e);	}
}

// ---------------------------------SET USER ---------------------------------------
// fiddler:
//user-agent:Fiddler
//content-type:application/json
//host:localhost

// --------------------------------- GET ALL ITEMS FROM HOUSE ---------------------


function getItemsFromHouse($houseID){
	try{
		$conn=getConnection();
		$stmt=$conn->prepare("SELECT * FROM item where house_ID=:houseID");
		$stmt->bindParam(":houseID",$houseID);
		$stmt->execute();
		$items=json_encode($stmt->fetchALL(PDO::FETCH_ASSOC));
		return $items;
	}catch(PDOException $e){	die($s);	}
}



// ---------------------------------ADD DETAILS-------------------------------------
function addUser(){
	$request=\Slim\Slim::getInstance()->request();
	$q=json_decode($request->getBody());
	try{
		$user=$q->user;
		$vKey=hash('sha256',microtime());
		
		$pass=$q->pass;
		//hash password
		$pass=hashPass($pass,$vKey);

		$houseID=$q->houseID;
	}catch(Exception $e){ die($e);}
	try{
		$conn=getConnection();
		$stmt=$conn->prepare("INSERT INTO user(username, password, vKey, house_ID) values(:user, :pass, :vKey, :houseID)");
		$stmt->bindParam(":user",$user);
		$stmt->bindParam(":pass",$pass);
		$stmt->bindParam(":vKey",$vKey);
		$stmt->bindParam(":houseID",$houseID);

		$stmt->execute();
	}catch(PDOException $e){ die($e);}
}
function addHouse(){
	$request=\Slim\Slim::getInstance()->request();
	$q=json_decode($request->getBody());
	
	// ---- try to hash the password and 
	// ---- 				set variables
	try{
		$user=$q->user;
		//password hashing
		$pass=$q->pass;
		$vKey=hash('sha256', microtime());//salt+validation key
		$pass=hashPass($pass, $vKey);
		// end -
		$hName=$q->hName;//house name
		$tokenID=generateRandomString();
	}catch(Exception $e){
		die("Please enter all required information!");
	}
	// ---- END ----

	try{
		// add a new 'admin' user
		$conn=getConnection();
		$stmt=$conn->prepare("INSERT INTO user(username, password, vKey) values(:user, :pass, :vKey)");
		$stmt->bindParam(":user",$user);
		$stmt->bindParam(":pass",$pass);
		$stmt->bindParam(":vKey",$vKey);
		$stmt->execute();
		$userID=$conn->lastInsertId();

		//add a new house
		$stmt=$conn->prepare("INSERT INTO house(name, tokenID, owner_User_ID) values(:name,:token,:owner)");
		$stmt->bindParam(":name",$hName);
		$stmt->bindParam(":token",$tokenID);
		$stmt->bindParam("owner",$userID);
		$stmt->execute();
		$houseID=$conn->lastInsertId();

		//set 'adminns' houseID to house we just made
		$stmt=$conn->prepare("UPDATE user SET house_ID=:houseID where user_ID=:userID");
		$stmt->bindParam(":houseID",$houseID);
		$stmt->bindParam(":userID",$userID);
		$stmt->execute();
		echo "added user $userID to house $houseID";

	}catch(PDOException $e){die($e);}
}

function addItem(){
	$request=\Slim\Slim::getInstance()->request();
	$q=json_decode($request->getBody());

	try{
		// required fields
		$houseID=$q->houseID;
		$prodName=$q->productName;
		$qty=$q->qty;
		$warningQty=$q->warningqty;

		//optional
		if(isset($q->barcode)) 		$barcode=$q->barcode;
		if(isset($q->categoryID)) 	$categoryID=$q->categoryID;

	}catch(Exception $e){ die("Please enter all required information!"); }

	try{
		$conn=getConnection();
		$stmt=$conn->prepare("INSERT INTO item(product_name, qty, warning_qty, house_ID, category_ID, barcode) 
			values(:prodName, :qty,:warningqty,:houseID, :categoryID,:barcode)");
		$stmt->bindParam(':prodName',$prodName);
		$stmt->bindParam(':qty',$qty);
		$stmt->bindParam(':warningqty',$warningQty);
		$stmt->bindParam(':houseID',$houseID);
		$stmt->bindParam(':categoryID',$categoryID);
		$stmt->bindParam(':barcode',$barcode);
		
		$stmt->execute();
		echo "Added item ID: ".$conn->lastInsertId();
	}catch(PDOException $e){ die($e); }
}


//   _____________________________________________________
/*	/	/	/	/	/	/	/	/	/	/	/	/	/	/ *
	***************************************************** /
	**									 				* /
	**	EMAIL AND PHONE NUMBER IS NOT NEEDED  			* /
	**													* /
	***************************************************** /
*/


// -------------------- LOGIN -------------------------
function login(){
	$request=\Slim\Slim::getInstance()->request();
	$q=json_decode($request->getBody());

	try{
		$user=$q->user;

		$isvKeyLogin=true;
		if(isset($q->pass))		$isvKeyLogin=false;

		if($isvKeyLogin){
			$vKey=$q->vkey;
			$validated=loginUserVkey($user,$vKey);
		}else{
			$vKey=getUserVKey($user);
			$pass=$q->pass;
			$pass=hashPass($pass, $vKey);
			$validated=loginUserPass($user, $pass);
			//echo $validated;

		}
		//echo getUserVKey('billk');
		if(!$validated)
			die("incorrect username or password");
		else
			echo "Logged in successfully";
	}catch(Exception $e){ 	die($e); }
}


// ---------------------- LOGIN VALIDATION ----------------------
function getUserVKey($username){
	try{
		$conn=getConnection();
		$stmt=$conn->prepare("SELECT vKey FROM user WHERE username=:user");
		$stmt->bindParam(":user",$username);
		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		return $row['vKey'];
	}catch(PDOException $e){
		die("User not found");
	}
}

function loginUserPass($user, $pass){
	try{
		$conn=getConnection();
		$stmt=$conn->prepare("SELECT * from user where username=:user and password=:pass");
		$stmt->bindParam(":user",$user);
		$stmt->bindParam(":pass",$pass);

		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		return isset($row['vKey']);

	}catch(PDOException $e) {die($e);}
}

function loginUserVkey($user, $vKey){
	try{
		$conn=getConnection();
		$stmt=$conn->prepare("SELECT * from user where username=:user and vKey=:vKey");
		$stmt->bindParam(":user",$user);
		$stmt->bindParam(":vKey",$vKey);

		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		return isset($row['vKey']);

	}catch(PDOException $e) {die($e);}
}

function hashPass($pass, $vKey){
	return hash('sha256',$vKey.$pass);
}
// ----------------------- END LOGIN VALIDATION ------------------











    function getAuthor($name){
    $hostname = 'localhost';
	$username = 'root';
	$password = 'root';
	$dbname = 'slimtest';
		try {
		    // First we need to get a connection object to the database server.
		   $sql="SELECT * from articles where author = '$name'";
		    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
		    $stmt = $dbh -> prepare($sql);
		    $stmt -> bindParam("id", $id);
		    $stmt -> execute();
		    $row = $stmt -> fetchALL(PDO::FETCH_OBJ);
		    $dbh = null;
		    echo json_encode($row);
		}catch(PDOException $e) {
		    if($dbh != null) $dbh = null;
		    echo $e -> getMessage();
		}
    }
    function getArticles() {
		$hostname = 'localhost';
	$username = 'root';
	$password = 'root';
	$dbname = 'slimtest';
    try {
        // First we need to get a connection object to the database server.
        
        $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username,
            $password);
        // now lets craft an SQL select string.
        $sql = "SELECT * from articles";
        // Make sql string into an SQL statement and execute the statement
        $stmt = $dbh -> prepare($sql);
        $stmt -> execute();
        // IMPORTANT to close connection after you have finished with it.
        // There could be hundreds of clients connecting to your site.
        // If you dont close connections to database this could
        // slow you system greatly.
        $row = $stmt -> fetchALL(PDO::FETCH_OBJ);
        $dbh = null;
        echo json_encode($row);
    }
    catch(PDOException $e) {
        if($dbh != null) $dbh = null;
        echo $e -> getMessage();
    }
}

    //GET route
    $app->get('/', function () {
         $template = <<<EOT
        <!DOCTYPE html>
         <html><head>
        <title>404 Page Not Found</title>
        <body><h1>404 Page Not Found</h1>
        <p>The page you are looking for could not be found. </p>
        </body></html>
EOT;
         echo $template;
     });
    $app->run();

    // GET route


function getArticle($id) {
	$hostname = 'localhost';
	$username = 'root';
	$password = 'root';
	$dbname = 'slimtest';
    $sql = "SELECT * from articles where id=:id";
    try {
        // First we need to get a connection object to the database server.
       
        $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
        $stmt = $dbh -> prepare($sql);
        $stmt -> bindParam("id", $id);
        $stmt -> execute();
        $row = $stmt -> fetch(PDO::FETCH_OBJ);
        $dbh = null;
        echo json_encode($row);
    }
    catch(PDOException $e) {
        if($dbh != null) $dbh = null;
        echo $e -> getMessage();
    }
}



function addArticle(){

	// Use slim to get the contents of the HTTP POST request

	$request = \Slim\Slim::getInstance()->request();

	// the request is in JSON format so we need to decode it

	$q = json_decode($request->getBody());

	// the rest of the code is just PDO stuff
	// Creat SQL INSERT STRING

	$sql = "INSERT INTO articles(title,date) VALUES (:title,:date)";
	try {

		// encapsulate connection stuff in a function

		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("title", $q->title);
		$stmt->bindParam("date", $q->date);
		$stmt->execute();
		$db = null;
	}

	catch(PDOException $e) {
		echo $e->getMessage();
	}
} // end function


function deleteArticle($id){
	$sql = "DELETE from articles WHERE id =	$id";
	try {
		$db = getConnection(); 
		$stmt=$db->prepare($sql); 
		$stmt->execute();

		$db = null;
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
}

// --------- bonus marks -------------
// --  --  -- UPDATE --   --   --
function updateArticle($id){
	// Use slim to get the contents of the HTTP POST request
	$request = \Slim\Slim::getInstance()->request();

	// the request is in JSON format so we need to decode it

	$q = json_decode($request->getBody());

	// the rest of the code is just PDO stuff
	// Creat SQL INSERT STRING
	//$sql = "UPDATE articles('title','date') VALUES ('$q->title','$q->date') where id='$id'";
    $sql="UPDATE articles SET title='$q->title', date='$q->date' where id=$id";
	try {

		// encapsulate connection stuff in a function
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("title", $q->title);
		$stmt->bindParam("date", $q->date);
		$stmt->execute();
		$db = null;
        echo "Updated!";
	}catch (PDOException $e) {
			echo $e->getMessage();
	}
}	


// encapsulate connection stuff in a function.
// When called this function returns a reference
// to a PDO Database connection object.
 /// ---------------------------------------------- CONNECTION ----------------------------------
function getConnection(){
	global $hostname, $username, $password, $dbname;
	try {

		// Create a PDO Connection Object,set Attributes and return
		// a reference to this connection object..

		$dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbh;
	}

	catch(PDOException $e) {
		echo "Error PDO Exception";
	}
} // end function

// --------------------------------------------------- RANDOM TOKEN GENERATOR --------------------
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>