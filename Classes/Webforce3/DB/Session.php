<?php

namespace Classes\Webforce3\DB;

//liée a config car on fait appel dans cette page
use Classes\Webforce3\Config\Config;
use Classes\Webforce3\Exceptions\InvalidSqlQueryException;

class Session extends DbObject {

    /** @var Location */
    protected $location;

    /** @var Training */
    protected $training;

    /** @var int */
    protected $startDate;

    /** @var int */
    protected $endDate;

    /** @var int */
    protected $number;

    public function __construct($id = 0, $location = null, $training = null, $startDate = 0, $endDate = 0, $number = 0, $inserted = '') {
        if (empty($location)) {
            $this->location = new location();
        } else {
            $this->location = $location;
        }
        if (empty($training)) {
            $this->training = new training();
        } else {
            $this->training = $training;
        }
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->number = $number;

        parent::__construct($id, $inserted);
    }

    /**
     * @param int $id
     * @return bool|Session
     * @throws InvalidSqlQueryException
     */
    public static function get($id) {
        $sql = '
            SELECT ses_id, location_loc_id, training_tra_id, ses_start_date, ses_end_date, ses_number
            FROM session
            WHERE ses_id = :id
	';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            throw new InvalidSqlQueryException($sql, $stmt);
        } else {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!empty($row)) {
                $currentObject = new Session(
                        $row['ses_id'], new Location($row['location_loc_id']), new Training($row['training_tra_id']), $row['ses_start_date'], $row['ses_end_date'], $row['ses_number']
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
        $returnList = array();

        $sql = '
            SELECT ses_id, location_loc_id, training_tra_id, ses_start_date, ses_end_date, ses_number
            FROM session
            WHERE ses_id = :id
	';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        if ($stmt->execute() === false) {
            throw new InvalidSqlQueryException($sql, $stmt);
        } else {
            $allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($allDatas as $row) {
                $currentObject = new Session(
                        $row['ses_id'], new Location($row['location_loc_id']), new Training($row['training_tra_id']), $row['ses_start_date'], $row['ses_end_date'], $row['ses_number']
                );
                $returnList[] = $currentObject;
            }
        }

        return $returnList;
    }

    /**
     * @return array
     */
    public static function getAllForSelect() {
        $returnList = array();

        $sql = '
            SELECT ses_id, tra_name, ses_start_date, ses_end_date, loc_name
            FROM session
            LEFT OUTER JOIN training ON training.tra_id = session.training_tra_id
            LEFT OUTER JOIN location ON location.loc_id = session.location_loc_id
            WHERE ses_id > 0
            ORDER BY ses_start_date ASC
        ';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        if ($stmt->execute() === false) {
            print_r($stmt->errorInfo());
        } else {
            $allDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($allDatas as $row) {
                $returnList[$row['ses_id']] = '[' . $row['ses_start_date'] . ' > ' . $row['ses_end_date'] . '] ' . $row['tra_name'] . ' - ' . $row['loc_name'];
            }
        }

        return $returnList;
    }

    /**
     * 
     * @return boolean
     * @throws InvalidSqlQueryException
     */
    public function saveDB() {
        if ($this->id > 0) {
            $sql = '
                    			
                UPDATE session
                SET ses_start_date = :startDate,
                ses_end_date = :endDate,
                location_loc_id = :locId,
                training_tra_id = :traId
                WHERE ses_id = :id
            ';
            $stmt = Config::getInstance()->getPDO()->prepare($sql);
            $stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
            $stmt->bindValue(':startDate', $this->startDate, \PDO::PARAM_INT);
            $stmt->bindValue(':endDate', $this->endDate, \PDO::PARAM_INT);
            $stmt->bindValue(':locId', $this->location->id, \PDO::PARAM_INT);
            $stmt->bindValue(':traId', $this->training->id, \PDO::PARAM_INT);


            if ($stmt->execute() === false) {
                throw new InvalidSqlQueryException($sql, $stmt);
            } else {
                return true;
            }
        } else {
            $sql = '
                INSERT INTO session (ses_id, ses_start_date, ses_end_date, loc_name, tra_name)
                VALUES (:id, :startDate, :endDate, :locId, :traId)
            ';
            $stmt = Config::getInstance()->getPDO()->prepare($sql);
            $stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
            $stmt->bindValue(':startDate', $this->startDate, \PDO::PARAM_INT);
            $stmt->bindValue(':endDate', $this->endDate, \PDO::PARAM_INT);
            $stmt->bindValue(':locId', $this->location->id, \PDO::PARAM_INT);
            $stmt->bindValue(':traId', $this->training->id, \PDO::PARAM_INT);

            if ($stmt->execute() === false) {
                throw new InvalidSqlQueryException($sql, $stmt);
            } else {
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
            DELETE FROM session WHERE ses_id = :id
	';
        $stmt = Config::getInstance()->getPDO()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            print_r($stmt->errorInfo());
        } else {
            return true;
        }
        return false;
    }

    /**
     * @return Session
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @return City
     */
    public function getTraining() {
        return $this->training;
    }

    /**
     * @return string
     */
    public function getstartDate() {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function getendDate() {
        return $this->endDate;
    }

    /**
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

}
