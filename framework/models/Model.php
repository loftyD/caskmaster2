<?php
/**
 *  Model.php
 *	All models should extend off this class.
 */
namespace models;
use \PDO;
use flight\Engine;
use components\EntityFactory;

abstract class Model {
	protected $primary_key, $table_name, $name_field;
	protected $db;

	const STATUS_ACTIVE = 'active';
	const STATUS_DISABLED = 'disabled';
	const STATUS_DELETED = 'deleted';

	/**
	 * Model Constructor
	 */
	final public function __construct() { 
		
		$dsn = $_SERVER['app']->get("db.vendor") . ':host=' . $_SERVER['app']->get("db.host") . ';dbname=' . $_SERVER['app']->get("db.name");
		$this->db = new \PDO($dsn,$_SERVER['app']->get("db.user"),$_SERVER['app']->get("db.password"), array(
			PDO::ATTR_PERSISTENT => true,
			)
		);
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->getModelProperties();
		if(is_array($this->primary_key)) {
			foreach($this->primary_key as $pk) {
				if(!empty($this->{$pk})) {
					$this->loadConstructedRelations();
				}
			}
		} else {
			if(!empty($this->{$this->primary_key})) {
				$this->loadConstructedRelations();
			}
		}
	}

	/**
	 * Returns the primary key and table name for this model.
	 * @return [type] [description]
	 */
	final protected function getModelProperties() {

		$sql = "SELECT primary_key_field, `table`,`name_field` FROM entity_factory where `class` = :class;";
		$statement = $this->db->prepare($sql);
		$statement->execute(array(
			':class' => (new \ReflectionClass($this))->getShortName(),
		));
		$statement->setFetchMode(\PDO::FETCH_OBJ);
		$result = $statement->fetch();
		if(empty($result)) {
			$result = false;
		}
		$multiPrimaryKeys = explode(",",$this->primary_key);
		if(count($multiPrimaryKeys) > 1) {
			$i = 0;
			foreach($multiPrimaryKeys as $pk) {
				$this->primary_key[$i] = $pk;
				$i++;
			}
		} else {
			$this->primary_key = $result->primary_key_field;
		}
		$this->table_name  = $result->table;
		$this->name_field  = $result->name_field;

	}

	/**
	 * Finds the record for this model by primary key and returns it as a Model instance.
	 * @param  mixed $id The primary key value for this model.
	 * @return Model
	 */
	public function findByPk($id) {
		$sql = "SELECT * FROM `". $this->table_name . "` WHERE `" .$this->primary_key . "` = :id";
		$statement = $this->db->prepare($sql);
		$statement->execute(array(
			':id' => $id,
		));
		$statement->setFetchMode(\PDO::FETCH_CLASS,get_class($this));
		$result = $statement->fetch();
		if(empty($result)) {
			$result = false;
		}

		$result->loadConstructedRelations();
		return $result;
	}

	/**
	 * Finds all the records for this model and returns it as a Model instance.
	 * @param  string $sort How the results should be ordered. Defaults to ASC (Ascending), for descending replace with DESC.
	 * @return Model
	 */
	public function findAll($sort="ASC") {
		$sql = "SELECT * FROM `". $this->table_name . "` ORDER BY `". $this->primary_key ."` $sort;";
		$statement = $this->db->prepare($sql);
		$statement->execute();
		$statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE,get_class($this));
		$result = $statement->fetchAll();
		if(empty($result)) {
			$result = false;
		}
		$result->loadConstructedRelations();
		return $result;
	}

	/**
	 * Returns the model's applicable attributes
	 * @return array
	 */
	public function getModelAttributes() {
		
		$q = $this->db->prepare("DESCRIBE " . $this->table_name);
		$q->execute();
		$table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
		foreach($table_fields as $index => $field) {
			$this->{$field} = "";
		}

		return array_flip($table_fields);
	}

	/**
	 * Handles the saving of a new model instance.
	 * @return boolean Returns the Insert ID upon successful save.
	 */
	public function save() {
		$app = $_SERVER['app'];
		$data = $app->request()->data;
		$store = array();

		foreach($this->getModelAttributes() as $field) {
			$store[$field] = $data[$field];
		}
		$list = implode(", ",$this->getModelAttributes());
		$sql = "INSERT INTO `" . $this->table_name . "` (" . $list .") VALUES (";

		foreach($this->getModelAttributes() as $field) {
			$sql .= ":$field, ";
		}
		$sql = rtrim($sql,", ");
		$sql .= ");";
		
		$statement = $this->db->prepare($sql);

		foreach($this->getModelAttributes() as $field) {
			$statement->bindParam(":$field", $store[$field]);
		}

		$result = $statement->execute();

		if(!$result) {
			return false;
		}
		
		return $this->db->lastInsertId();

	}

	/**
	 * Fetches related records for a model.
	 * @param  string  $identifier              The relation data will be stored and accessed under this property
	 * @param  string  $targetModel             The class name of the corresponding model in which to bring back records.
	 * @param  string  $throughModel            Used if the relation data you need is in a join table.
	 * @param  string  $field                   If you wish to use a different field for the WHERE statement.
	 * @param  string  $condition               An optional condition or conditions which you can pass in.
	 * @param  boolean $findOne                 Whether to return one record or all records that it finds.
	 * @param  string  $matchingForeignKeyField If the primary keys for the two models are not the same, you can override it here.
	 * @return Model                            Results from the relation.
	 */
	public function getRelated($identifier, $targetModel, $throughModel = null, $field = null, $condition = null, $findOne = true, $matchingForeignKeyField=null) {

		if(!empty($this->{$identifier})) {
			return $this->{$identifier};
		}
		$fqnp = new \ReflectionClass("\models\\". $targetModel);
		$fqnp = $fqnp->getName();

		if(class_exists($fqnp) === false) {
			throw new \Exception('$targetModel '. $targetModel .' does not exist');
		}

		if(empty($this->{$this->primary_key})) {
			throw new \Exception('Model instance needs data');
		}

		if(!isset($foreignModel)) {
			$foreignClass = $targetModel;
			$entityFactory = new EntityFactory($targetModel);
			$foreignModel = $entityFactory->createFromClass();
		}

		if($throughModel != null) {
			$throughClass = $throughModel;
			$entityFactory = new EntityFactory($throughModel);
			$throughModel = $entityFactory->createFromClass();
		}

		if($matchingForeignKeyField != null) {
			if($this->primary_key != $foreignModel->primary_key) {
				$foreignModel->primary_key = $matchingForeignKeyField;
			}
		}


		if($field != null) {
			$whereField = $field;
		} else {
			if(is_array($this->primary_key)) {
				$i = 0;
				foreach($this->primary_key as $pk) {
					$whereField[$i] = $pk;
					$i++;
				}
			} else {
				$whereField = $this->primary_key;
			}
		}

		$sql = "SELECT * FROM `" . $foreignModel->table_name . "` `t` ";

		if($throughModel != null) {
			$splitThroughModelPk = explode(",",$throughModel->primary_key);

			$splitForeignModelPk = explode(",",$foreignModel->primary_key);
			$splitForeignModelPk = array_flip($splitForeignModelPk);


			if(count($splitForeignModelPk) > 1) {
				$splitThroughModelPk = reset($splitThroughModelPk);
				$index = $splitForeignModelPk[$splitThroughModelPk];
				$splitForeignModelPk = array_flip($splitForeignModelPk);
				$foreignModelPrimaryKey = $splitForeignModelPk[$index];
			} else {
				$foreignModelPrimaryKey = $foreignModel->primary_key;
			}

			if(is_array($splitThroughModelPk) && count ($splitThroughModelPk) > 1) {
				
				foreach($splitThroughModelPk as $pk) {
					$entityFactoryInstance = new EntityFactory($pk);
					$pk = $entityFactoryInstance->createFromPrimaryKey($throughClass);
					if($pk->table_name == $identifier) {
						$sql .= " INNER JOIN `" . $throughModel->table_name . "` `" 
						. $throughModel->table_name . "` ON `" . $throughModel->table_name . "`.`" 
						. $pk->primary_key . "` = `t`.`" . $foreignModelPrimaryKey . "` ";
					}
				}

			} else {			
				$sql .= " INNER JOIN `" . $throughModel->table_name . "` `" 
				. $throughModel->table_name . "` ON `" . $throughModel->table_name . "`.`" 
				. $throughModel->primary_key . "` = `t`.`" . $foreignModelPrimaryKey . "` ";
			}
			// INNER JOIN `users_auth_status` `users_auth_status` ON `users_auth_status`.`user_id` = `t`.`group_id`
		}

		if(is_array($whereField)) {
			$i= 0;
			$sql .= "WHERE";
			foreach($whereField as $where) {
				$alias = $this->returnAlias($where);
				if(count($alias) == 1) {
					$aliasIdentifier = 't';
					$fieldFromAlias = $alias[0];
				} else {
					$aliasIdentifier = $alias[0];
					$fieldFromAlias = $alias[1];
				}

				$sql.= " `" . $aliasIdentifier . "`.`" . $fieldFromAlias . "` = :thisPrimaryKey$i";
				if($i < count($whereField)) {
					$sql.= " AND";
				}
				$params = array(":thisPrimaryKey$i" => $this->{$this->primary_key[$i]});
				$i++;
			}
		} else {
			$alias = $this->returnAlias($whereField);
			if(count($alias) == 1) {
				$aliasIdentifier = 't';
				$fieldFromAlias = $alias[0];
			} else {
				$aliasIdentifier = $alias[0];
				$fieldFromAlias = $alias[1];
			}

			$sql .= "WHERE `" . $aliasIdentifier . "`.`" . $fieldFromAlias . "` = :thisPrimaryKey";

		}
		if($condition != null) {
			$sql .= " AND $condition";
		}
		$params = array(":thisPrimaryKey" => $this->{$this->primary_key});

		if(empty($this->{$identifier})) {
			$this->{$identifier} = $foreignModel->executeSql($sql,$params);
		}

		if($findOne) {
			$this->{$identifier} = reset($this->{$identifier});
		}
		return $this->{$identifier};
	}

	/**
	 * Executes sql and returns results.
	 * @param  string $sql    The SQL
	 * @param  array  $params An array of parameters for the sql.
	 * @return Model         The results.
	 */
	public function executeSql($sql,$params=array(),$fetchAll=true) {
		$statement = $this->db->prepare($sql);
		$statement->execute($params);
		$statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE,get_class($this));
		if($fetchAll) {
			$result = $statement->fetchAll();
			$count = count($result);	
			if($count > 0) {
				foreach((object)$result as $each) {
					$each->loadConstructedRelations();
				}
			}
		}
		else {
			$result = $statement->fetch();
			$result->loadConstructedRelations();
		}


		return $result;
	}

	/**
	 * If you want a model to use relations, then define your relations in this method. 
	 * See models\User for an example.
	 * @param  Model  $resource The model instance
	 * @return boolean	This method should return true if a model is using relations
	 */
	public function loadRelations(Model $resource) {
		return false;
	}

	/**
	 * This is called by the constructor or when using findByPk() or other similar methods.
	 * This will instantiate the relations for this model.
	 * @return [type] [description]
	 */
	final public function loadConstructedRelations() {
		if(isset($this->{$this->primary_key})) {
			$this->loadRelations($this);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * This method splits an aliased field into the alias and field name, and returns an array.
	 * @param  string $str the field in question.
	 * @return array     array
	 */
	public function returnAlias($str) {
		$str = str_replace("`","",$str);
		$data = explode(".",$str);
		if(count($data) == 1) {
			$r[0] = 't';
			$r[1] = $data[0];
		} else {
			$r[0] = $data[0];
			$r[1] = $data[1];
		}

		return $r;
	}

	/**
	 * Returns 1 record via a match of given attributes and values. 
	 * @param  array  $attribs   An array containing matching fields and values.
	 * @param  string $condition An optional condition for this query.
	 * @return Model            A model with 1 result and any associated relation data.
	 */
	public function findByAttributes($attribs=array(),$condition=null) {
		if(empty($attribs)) {
			throw new \Exception(get_class($this) . "::findByAttributes() must define an array");
		}

		$sql = "SELECT * FROM `" . $this->table_name . "` `t` WHERE ";
		$i = 0;
		$count = count($attribs);
		foreach($attribs as $attribute => $value) {
			$i++;
			$sql .= "$attribute = :$attribute ";
			if($count > 1 && $i < $count) 
				$sql.= "AND ";
		}

		if($condition != null) {
			$sql .= " AND $condition";
		}
		$statement = $this->db->prepare($sql);

		foreach($attribs as $attribute => $value) {
			$statement->bindParam(":$attribute", $value);
		}

		$statement->execute();
		$statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE,get_class($this));
		$result = $statement->fetch();
		$result->loadConstructedRelations();

		return $result;
	}

	/**
	 * Returns ~ records via a match of given attributes and values. 
	 * @param  array  $attribs   An array containing matching fields and values.
	 * @param  string $condition An optional condition for this query.
	 * @return Model            A model with 1 result and any associated relation data.
	 */
	public function findAllByAttributes($attribs=array(),$condition=null) {
		if(empty($attribs)) {
			throw new \Exception(get_class($this) . "::findByAttributes() must define an array");
		}

		$sql = "SELECT * FROM `" . $this->table_name . "` `t` WHERE ";
		$i = 0;
		$count = count($attribs);
		foreach($attribs as $attribute => $value) {
			$i++;
			$sql .= "$attribute = :$attribute ";
			if($count > 1 && $i < $count) 
				$sql.= "AND ";
		}

		if($condition != null) {
			$sql .= " AND $condition";
		}
		$statement = $this->db->prepare($sql);

		foreach($attribs as $attribute => $value) {
			$statement->bindParam(":$attribute", $value);
		}

		$statement->execute();
		$statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE,get_class($this));
		$result = $statement->fetchAll();
		if($count > 0) {
			foreach((object)$result as $each) {
					$each->loadConstructedRelations();
			}
		}

		return $result;
	}
}
