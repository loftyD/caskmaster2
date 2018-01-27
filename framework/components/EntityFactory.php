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

	public function init(){
		$dsn = $_SERVER['app']->get("db.vendor") . ':host=' . $_SERVER['app']->get("db.host") . ';dbname=' . $_SERVER['app']->get("db.name");
		$this->db = new \PDO($dsn,$_SERVER['app']->get("db.user"),$_SERVER['app']->get("db.password"), array(
			PDO::ATTR_PERSISTENT => true,
			)
		);
		$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
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

	public function createFromPrimaryKey() {
		$sql = "SELECT * FROM entity_factory WHERE `primary_key_field` = :table and status = 'active' ";
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