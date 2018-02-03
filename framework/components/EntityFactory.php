<?php
/**
 *  EntityFactory.php
 *	Returns an entity.
 */
namespace components;
use \PDO;
class EntityFactory {
	protected $db;
	protected $entityTable;
	/**
	 * Model Constructor
	 */
	final public function __construct($entityTable="") {
		$this->init();

		$this->entityTable = $entityTable;
	}

	/**
	 * Sets up pdo instance
	 * @return PDO
	 */
	public function init(){
		$dsn = $_SERVER['app']->get("db.vendor") . ':host=' . $_SERVER['app']->get("db.host") . ';dbname=' . $_SERVER['app']->get("db.name");
		$this->db = new \PDO($dsn,$_SERVER['app']->get("db.user"),$_SERVER['app']->get("db.password"), array(
			PDO::ATTR_PERSISTENT => true,
			)
		);
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * creates a model based on table name
	 * @return Model
	 */
	public function create() {
		$sql = "SELECT * FROM entity_factory WHERE `table` = :table and status = 'active' ";
		$statement = $this->db->prepare($sql);
		$statement->execute(array(
			':table' => $this->entityTable,
		));
		$result = $statement->fetch();
		if(empty($result)) {
			throw new \Exception("Entity must exist in entity_factory");
		}
		$class = "\\models\\$result->class";

		return new $class;
	}

	/**
	 * creates a model based on class name
	 * @return Model
	 */
	public function createFromClass() {
		$sql = "SELECT * FROM entity_factory WHERE `class` = :table and status = 'active' ";
		$statement = $this->db->prepare($sql);
		$statement->execute(array(
			':table' => $this->entityTable,
		));
		$result = $statement->fetch();
		if(empty($result)) {
			throw new \Exception("Entity must exist in entity_factory");
		}
		
		$result = ((object)$result);
		$class = "\\models\\$result->class";

		return new $class;
	}

	/**
	 * creates a model based on primary key name
	 * @return Model
	 */
	public function createFromPrimaryKey($useClass=false) {
		if($useClass) {
			$sql = "SELECT * FROM entity_factory WHERE FIND_IN_SET(:table,`primary_key_field`) and `class` = :class and status = 'active' ";
		} else {
			$sql = "SELECT * FROM entity_factory WHERE FIND_IN_SET(:table,`primary_key_field`) and status = 'active' ";
		}
		$statement = $this->db->prepare($sql);
		$statement->execute(array(
			':table' => $this->entityTable,
			':class' => $useClass,
		));

		$result = $statement->fetch();
		if(empty($result)) {
			throw new \Exception("Entity must exist in entity_factory");
		}
		
		$result = ((object)$result);
		$class = "\\models\\$result->class";

		return new $class;
	}

	/**
	 * Returns an object containg information regarding the entity.
	 * @return object
	 */
	public function getEntityInfoById($id) {
		$sql = "SELECT * FROM entity_factory WHERE `entity_factory_id` = :id and status = 'active' ";
		$statement = $this->db->prepare($sql);
		$statement->execute(array(
			':id' => $id,
		));
		$result = $statement->fetch();
		if(empty($result)) {
			throw new \Exception("Entity must exist in entity_factory");
		}

		return (object) $result;
	}
}