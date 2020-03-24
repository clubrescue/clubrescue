<?php
	
class Database {
	private $iniPath	   = "/srv/home/trbnu/domains/trb.nu/env.ini";
	
    private $dbh;
    private $error;
	private $stmt;
	
    public function __construct(){
		
		$ini_array = parse_ini_file($this->iniPath);
		$this->host      = $ini_array["DB_HOST_CR"];
		$this->user      = $ini_array["DB_USER_CR"];
		$this->pass      = $ini_array["DB_PASS_CR"];
		$this->dbname    = $ini_array["DB_NAME_CR"];
		
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8;';
		// Set options
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
			PDO::MYSQL_ATTR_LOCAL_INFILE => true
        );
        // Create a new PDO instanace
        try{
			//$this->dbh = new PDO($dsn, $GLOBALS["env_DB_USERNAME"], $GLOBALS["env_DB_PASSWORD"], $options);
			$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        // Catch any errors
        catch(PDOException $e){
            $this->error = $e->getMessage();
        }
    }
	//The prepare function allows you to bind values into your SQL statements
	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}
	//In order to prepare our SQL queries, we need to bind the inputs with the placeholders we put in place.
	public function bind($param, $value, $type = null){
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}
	
	//The execute method executes the prepared statement
	public function execute(){
        try{
			return $this->stmt->execute();
        }
        // Catch any errors
        catch(PDOException $e){
            $this->error = $e->getMessage();
			return $this->error;
        }		
	}
	
	//The Result Set function returns an array of the result set rows
	public function resultset(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	//Single method returns a single record from the database
	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	//Single method returns a single record from the database
	public function fetch(){
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	//Returns the number of effected rows from the previous delete, update or insert statement
	public function rowCount(){
		return $this->stmt->rowCount();
	}
	
	//Returns the last inserted Id as a string
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}
	
	//To begin a transaction
	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}
	
	//To end a transaction and commit your changes
	public function endTransaction(){
		return $this->dbh->commit();
	}
	
	//To cancel a transaction and roll back your changes
	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}
	
	//The Debug Dump Parameters methods dumps the the information that was contained in the Prepared Statement
	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}
}
?>