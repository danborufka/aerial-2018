<?php

require_once(__DIR__ . '/db.php');
require_once __DIR__ . "/dbstudent.php";

class DbVoucherRequest extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getReadResultObject($sql)
    {
        $result = new Result();
        try {
            $res = $this->getResults($sql);
            if (!empty($this->db->error)) {
                $result->errtxt = "Fehler:" . $this->db->error;
            } else {
                $result->error = 0;
                $result->data = $res;
            }
        } catch (exception $e) {
            $result->error = 1;
            $result->errtxt = $e->getMessage();
        }
        return $result;
    }

    public function getSingleReadResult($sql)
    {
        $result = new Result();
        try {
            $res = $this->getSingleResult($sql);
            if (!empty($this->db->error)) {
                $result->errtxt = "Fehler:" . $this->db->error;
            } else {
                $result->error = 0;
                $result->data = $res;
            }
        } catch (exception $e) {
            $result->error = 1;
            $result->errtxt = $e->getMessage();
        }
        return $result;
    }

    public function getWriteResultObject($sql)
    {
        $result = new Result();
        try {
            $res = $this->db->query($sql);
            if (!empty($this->db->error)) {
                $result->errtxt = "Fehler:" . $this->db->error;
            } else {
                $result->error = 0;
            }
        } catch (exception $e) {
            $result->error = 1;
            $result->errtxt = $e->getMessage();
        }
        return $result;
    }

    public function getErrorResultObject($errtxt)
    {
        $result = new Result();
        $result->errtxt = $errtxt;
        return $result;
    }

    public function getSearchResult()
    {
        $sql = "SELECT id, CONCAT(prename, ' ', surname) AS name, email, title, DATE_FORMAT(added, '%d.%m.%Y um %H:%i') AS adddate, payed
                FROM voucher
                  JOIN as_students ON student_id = voucher.student
                WHERE requested = 1
                ORDER BY added DESC;";

        return $this->getReadResultObject($sql);
    }

    public function changeState($parameter)
    {
        $p = $this->processParameter($parameter);
        if (empty($parameter['id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');

        $sqlUpdateApplication = "UPDATE voucher SET payed = IF(payed = 1,0,1) WHERE id = $p->id";

        $result = $this->getWriteResultObject($sqlUpdateApplication);

        return $result;

    }


}

?>