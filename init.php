<?php
// start of PHP code -------------

class API {
	var $host = "localhost";
	var $user = "root";
	var $pass = "";
	var $db = "testdb";
	var $table = "data";
	var $conn = null;
	var $selected_db = null;
	var $json = null;

	// function to initiate connection to DataBase and select it
	function connect(){
		$this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
		$this->selected_db = $this->conn->select_db($this->db);

		if (mysqli_connect_errno()){
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}else echo "connected to DB";
	}

	// function to encode JSON arrays returned from the requests
	function requestJSON($json){
		header('content-type: application/json');
		$encoded = json_encode($json);
		echo "$encoded";
	}


	// function to handle requests
	function request ($server_request_method){
		// handling the POST request
		if ($server_request_method] == "POST"){
			$name = isset($_POST['name'])? $this->conn->real_escape_string($_POST['name']) : "";
			$id = isset($_POST['id'])? $this->conn->real_escape_string($_POST['id']): "";
			$job = isset($_POST['job'])? $this->conn->real_escape_string($_POST['job']): "";
			$age = isset($_POST['age'])? $this->conn->real_escape_string($_POST['age']): "";

			if ($id == ""){
				echo "\nNO ID!!";
			}else
			$insert_query = "INSERT $this->table (name, job, age, id) VALUES ('$name', '$job', '$age', '$id')";
			$query = $this->conn->query($insert_query);

			if($query){
				$post_json = array("status" => "1", "msg" => "Done! data added!");
				$this->closeConnection();
				$this->requestJSON($post_json);
			}else{
				$post_json = array("status" => "0", "msg" => "ERROR ADDING DATA..", "query" => $query, "error" => $this->conn->error);
				$this->closeConnection();
				$this->requestJSON($post_json);
			}

			// handling the GET request
		}else if ($server_request_method == "GET"){
			$id = isset($_GET['id'])? $this->conn->real_escape_string($_GET['id']) : "" ;

			if (!empty($id)){
				$select_query = "SELECT name, job FROM $this->table where id=$id";
				$get_query = $this->conn->query($select_query);
				$result = array();

				while ($r = mysqli_fetch_array($get_query)){
					extract($r);
					$result[] = array("name"=>$name, "job"=>$job);
				}
				
				$get_json = array("status"=>1, "info"=>$result[0]);
				$this->closeConnection();
				$this->requestJSON($get_json);
			}else{
				$get_json = array("status"=>0, "msg"=>$id);
				$this->closeConnection();
				$this->requestJSON($get_json);
			}
		}else echo "request type not yet supported!";

	}

	// function to close connection
	function closeConnection (){
		$this->conn->close();
		echo "\n connection closed";
	}

}

// getting object of API class and invoiking it's functions

$api_test = new API;

$api_test->connect();
$api_test->request($_SERVER['REQUEST_METHOD']);

// end of PHP code -------------
?>
