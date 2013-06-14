<?php 

/* Author Bawyka T.P.
 * site https://github.com/Bawyka/dbfeature
 * version 2.0
 * License Free
 */
 
# ДРАЙВЕР ДЛЯ РАБОТЫ С БАЗОЙ ДАННЫХ
class DBFeature {

	protected $DB_HOST = "localhost";
	protected $DB_NAME = "test";
	protected $DB_USER = "root";
	protected $DB_PASS = "";
	
	public $CHARSET = "UTF-8";
	
	protected $id = false;

	// current table
	protected $tbl = false;

	function __construct(){
	
		try
		{
			$this->pdo = new PDO("mysql:host=".$this->DB_HOST.";dbname=".$this->DB_NAME.";charset=".$this->CHARSET,$this->DB_USER,$this->DB_PASS);
		}
		catch(PDOException $e)
		{
			die("Error ". $e->getMessage());
		}
		
	}
		
	// __call установит таблицу
	public function __call( $method, $param )
	{
		$tables = array();
		
		if ($result = $this->pdo->query("SHOW TABLES"))
		{
			while ($row = $result->fetch(PDO::FETCH_NUM))
			{			
				$tables[] = $row[0];
			}
		}
		
		if (in_array($method,$tables))
		{
			$this->tbl = $method;
			
			// Параметр $param (array) по умолчанию является массивом
			if (isset($param) and count($param)>0)
			{
				// Если только одно число передано, это будет $id
				if (count($param)==1 and is_numeric($param[0]))
				{
					$this->id = $param[0];
				}
			}
		}
		else
		{
			echo "Table $method doesn't exists! <br />";
		}
		
		return $this;
	}
	
	// Loading as Object
	public function Load()
	{
		// Existance Check
		if ($this->id and $this->ExistsRow('id',$this->id))
		{
			return (object)$this->GetOne('*','id',$this->id);
		}
		
		return false;
	}
	
	/* Query
	 * @param $query (string)
	 * @return bool
	 */
	public function Query($query)
	{
		if ($result = $this->pdo->query($query))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/* getting all data from table 
	 * out @return (associative array) $data
	 */
	public function GetData()
	{	
		if (!$this->tbl) return false;
	
		if ($result = $this->pdo->prepare("SELECT * FROM ".$this->tbl.""))
		{
			$result->execute();
			$data = $result->fetchAll(PDO::FETCH_ASSOC);
			
			return $data;
		}
	}
	
	/* Delete One Record
	 * @param (int) $idn - identifier of record
	 * @param $val - value of the identifier
	 * @return bool
	 */
	public function DelOne($clm, $val)
	{
		if (!$this->tbl) return false;
	
		if ($result = $this->pdo->prepare("DELETE FROM `".$this->tbl."` WHERE `".$clm."`=:val LIMIT 1"))
		{
			$result->bindValue(":val",$val);
			$result->execute();
		}
		
		if ($result = $this->pdo->prepare("SELECT COUNT(*) FROM `".$this->tbl."` WHERE `".$clm."`=:val"))
		{
			$result->bindValue(":val",$val);
			$result->execute();
			$nRows = $result->fetchColumn();
			
			if ($nRows === "0" or $nRows===0)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		return FALSE;
	}
	
	/* Checking if the Row exists in the table or No.
	 * @param (string) $clm - column name
	 * @param (string/int) $rc - record value
	 */
	public function ExistsRow($clm,$rc)
	{
		if (!$this->tbl) return false;
	
		if ($result = $this->pdo->prepare("SELECT COUNT(*) as c FROM `".$this->tbl."` WHERE `".$clm."`=:rc LIMIT 1"))
		{
			$result->bindValue(":rc",$rc);
			$result->execute();
			$nRows = $result->fetchColumn();
			
			if ($nRows==="0" or $nRows===0)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/* Update One Record
	 * @param (string) $clm - Column Name
	 * @param (string/int) $val - value
	 * @return bool
	 */
	public function UpdOne($clm,$upd,$idn,$val)
	{
		if (!$this->tbl) return false;
	
		if ($result = $this->pdo->prepare("UPDATE `".$this->tbl."` SET `".$clm."`=:upd WHERE `".$idn."`=:val LIMIT 1"))
		{
			$result->bindValue(":upd",$upd);
			$result->bindValue(":val",$val);
			$result->execute();
			$count = $result->rowCount();
			
			if ($count===0)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/* Get One Data
	 * @param (string) $clm - column of wanted data
	 * @param $idn - identifier of WHERE
	 * @param $val - identifier value
	 * @return $data
	 */
	public function GetOne($clm,$idn,$val)
	{
		if (!$this->tbl) return false;
	
		if ($result = $this->pdo->prepare("SELECT ".$clm." FROM `".$this->tbl."` WHERE `".$idn."`=:val LIMIT 1"))
		{
			$result->bindValue(":val",$val);
			$result->execute();
			$assoc = $result->fetch(PDO::FETCH_ASSOC);
		
			return $assoc;
		}
		
		return FALSE;
	}
	
	/* count some rows
	 * @param (string) $clm - requested column
	 * @param $idn - identifier of row
	 * @param $val - value of row
	 * @return (int) $count
	 */
	public function CountRows($clm,$idn,$val)
	{
		if (!$this->tbl) return false;
	
		if ($result = $this->pdo->prepare("SELECT `".$clm."` FROM `".$this->tbl."` WHERE `".$idn."`=:val"))		
		{
			$result->bindValue(":val",$val);
			$result->execute();
			$count = $result->rowCount();
		
			return $count;
		}
		
		return FALSE;
	}
	
	/* fetch whole row 
	 * @param $idn - identifier
	 * @param $val - value of identifier
	 */
	public function GetRec($idn,$val)
	{
		if (!$this->tbl) return false;
	
		if ($result = $this->pdo->prepare("SELECT * FROM `".$this->tbl."` WHERE `".$idn."`=:val LIMIT 1"))
		{
			$result->bindValue(":val",$val);
			$result->execute();
			$data = $result->fetch(PDO::FETCH_ASSOC);
			
			if (is_array($data)) 
			{
				return $data;
			}
			else
			{
				return FALSE;
			}
		}
		
		return FALSE;
	}

	/* Get Every Record 
	 * @param (string) $clm  - name of the requested column
	 * @param (string) $idn - identifier
	 * @param (string) $val - value of identifier
	 * @return (associative array) $data
	 */
	public function GetEvery($clm,$idn,$val)
	{
		if (!$this->tbl) return false;
	
		if ($result = $this->pdo->prepare("SELECT `".$clm."` FROM `".$this->tbl."` WHERE `".$idn."`=:val"))
		{
			$result->bindValue(":val",$val);
			$result->execute();
			$data = $result->fetchAll(PDO::FETCH_ASSOC);
			
			if (is_array($data))
			{
				return $data;
			}
			else
			{
				return FALSE;
			}
		}
		
		return FALSE;
	}
	
	/* Insertion
	 * @param (array) $params - array like "field"=>"value"
	 * @return (int) lastinserted identifier $lId
	 */
	public function PutData($params = array())
	{
		if (!$this->tbl) return false;
		
		$f = "("; $v = "("; $q = "";
		
		foreach ($params as $key => $val) 
		{
			$f.=$key.",";
			$v.=$val.",";
			$q.="?,";
		}
		
		$f = substr_replace($f ,"",-1);
		$v = substr_replace($v ,"",-1);
		$q = substr_replace($q ,"",-1);
		
		$sql = "INSERT INTO `".$this->tbl."`".$f.") VALUES ($q)";
		
		$i = 1;
		
		if ($result = $this->pdo->prepare($sql))
		{
			foreach ($params as $key => $val) 
			{
				$result->bindValue($i,$val);
				$i++;
			}
		
			$result->execute();
			$lId = $this->pdo->lastInsertId();
			
			return $lId;
		}
	}
	
	/* 
	 * Checking is Empty Table
	 */
	public function isEmpty()
	{
		if (!$this->tbl) return false;
		
		if ($result = $this->pdo->query("SELECT COUNT(*) as c FROM ".$this->tbl))
		{
			$result->execute();
			
			$empty = $result->fetch(PDO::FETCH_ASSOC);
		}
						
		if ($empty['c']==="0" or $empty['c']===0) 
			return true;
		else
		 return false;
	}
	
	/* Set table
	 * @param (int) $t - table name
	 */
	public function SetTable( $t )
	{	
		if (!empty($t))
		{
			$this->tbl = $t;
		}
		else
		{
			return FALSE;
		}
	}
}
