
<?php 
	
	/*
	* Created by Belal Khan
	* website: www.simplifiedcoding.net 
	*/
	
	//Class DbConnect
	class DbConnect
	{
		//Variable to store database link
		private $con;
	 
		//Class constructor
		function __construct()
		{
	 
		}
	 
		//This method will connect to the database
		function connect()
		{
			//Including the constants.php file to get the database constants
			include_once dirname(__FILE__) . '/Constants.php';
	 
			//connecting to mysql database
			$this->con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	 		mysqli_set_charset($this->con, "utf-8");
			mysqli_query($this->con, "SET NAMES utf8");
			//Checking if any error occured while connecting
			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	 
			//finally returning the connection link 
			return $this->con;
		}
	 
	}
