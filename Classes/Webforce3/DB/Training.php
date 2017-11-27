<?php

namespace Classes\Webforce3\DB;

use Classes\Webforce3\Config\Config;
use Classes\Webforce3\Exceptions\InvalidSqlQueryException;

class Training extends DbObject {

    /** @var string */
    protected $name;


    public function __construct($id = 0, $name = '', $inserted = '') {
        
        $this->name = $name;

        parent::__construct($id, $inserted);
    }

    /**
     * @param int $id
     * @return bool|Training
     * @throws InvalidSqlQueryException
     */
    public static function get($id) {
        // TODO: Implement get() method.
        $sql = '
            SELECT tra_id
            FROM training
            WHERE tra_id = :id
        ';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            throw new InvalidSqlQueryException($sql, $stmt);
        } else {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!empty($row)) {
                $currentObject = new Training(
                        $row['tra_id'], 
                        $row['tra_name']
                );
                return $currentObject;
            }
        }
        return false;
    }

    /**
     * @return DbObject[]
     * @throws InvalidSqlQueryException
     */
    public static function getAll() {
        // TODO: Implement getAll() method.
        $returnList = array();

        $sql = '
			SELECT tra_id, tra_name
			FROM training
			WHERE tra_id > 0
			
		';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        if ($stmt->execute() === false) {
            throw new InvalidSqlQueryException($sql, $stmt);
        } else {
            $allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($allDatas as $row) {
                $currentObject = new Training(
                        $row['tra_id'], 
                        $row['tra_name']
                        
                );
                $returnList[] = $currentObject;
            }
        }
        return $returnList;
    }

    /**
     * @return array
     * @throws InvalidSqlQueryException
     */
    public static function getAllForSelect() {
        $returnList = array();

        $sql = '
			SELECT tra_id, tra_name
			FROM training
			WHERE tra_id > 0
			ORDER BY tra_name ASC
		';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        if ($stmt->execute() === false) {
            print_r($stmt->errorInfo());
        } else {
            $allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($allDatas as $row) {
                $returnList[$row['tra_id']] = $row['tra_name'];
            }
        }

        return $returnList;
    }
    
   
    /**
    * @return bool
    * @throws InvalidSqlQueryException
    */
        
   public function saveDB() {
		if ($this->id > 0) {
			$sql = '
                            UPDATE training
                            SET tra_name = :name,
                            WHERE tra_id = :id
			';
			$stmt = Config::getInstance()->getPDO()->prepare($sql);
			$stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
                        $stmt->bindValue(':name', $this->name);
						
			if ($stmt->execute() === false) {
				throw new InvalidSqlQueryException($sql, $stmt);
			}
			else {
				return true;
			}
		}
		else {
			$sql = '
                            INSERT INTO training (tra_name)
                            VALUES (:name)
			';
			$stmt = Config::getInstance()->getPDO()->prepare($sql);
                        $stmt->bindValue(':name', $this->name);
			
			

			if ($stmt->execute() === false) {
				throw new InvalidSqlQueryException($sql, $stmt);
			}
			else {
				$this->id = Config::getInstance()->getPDO()->lastInsertId();
				return true;
			}
		}

		return false;
	}


	/**
	 * @param int $id
	 * @return bool
	 * @throws InvalidSqlQueryException
	 */
	public static function deleteById($id) {
		$sql = '
                    DELETE FROM training WHERE tra_id = :id
		';
		$stmt = Config::getInstance()->getPDO()->prepare($sql);
		$stmt->bindValue(':id', $id, \PDO::PARAM_INT);

		if ($stmt->execute() === false) {
			print_r($stmt->errorInfo());
		}
		else {
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	
}
