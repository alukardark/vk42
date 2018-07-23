<?php

class MyConnection
{

    private $dbh;
    private $usedDB = false;
    private $error  = false;

    public function __construct()
    {
        //die("DBHost". DBHost);
        try
        {
            $this->dbh    = new PDO("mysql:host=" . DBHost . ";charset=utf8", DBLogin, DBPassword);
            $this->dbh->query("USE `" . DBName . "`");
            $this->usedDB = DBName;
            //$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            //$this->dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            //$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            $this->error = "Не удалось установить соединение <br/>" . $e->getMessage();
        }
    }

    public function close()
    {
        $this->dbh    = null;
        $this->usedDB = false;
    }

    public function dropCarTables()
    {
        if (!$this->dbh || !$this->usedDB)
        {
            return false;
        }

        $tables = array(
            array("TABLE_NAME" => "tx_carmodels"),
            array("TABLE_NAME" => "tx_specificationtype"),
            array("TABLE_NAME" => "tx_tyrespecifications"),
            array("TABLE_NAME" => "tx_wheelspecifications"),
        );

        $countQueries = count($tables);
        if (!$countQueries)
        {
            return true;
        }

        $lastCountQueriesErrors = 0;
        $arTablesDeleted        = array();
        $deleteSuccess          = false;
        $doQuery                = true;
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        while ($doQuery)
        {
            $countQueriesErrors = 0;
            $errorsLog          = array();

            foreach ($tables as $value)
            {
                $table = $value['TABLE_NAME'];

                if (in_array($table, $arTablesDeleted))
                {
                    continue;
                }
                try
                {
                    $this->dbh->query("DROP TABLE `{$table}`;");
                }
                catch (PDOException $e)
                {
                    $countQueriesErrors++;
                    $errorsLog[] = $e->getMessage();
                    if ($countQueriesErrors == $countQueries || $countQueriesErrors == $lastCountQueriesErrors)
                    {
                        $doQuery = false;
                        break;
                    }
                    continue;
                }
                $arTablesDeleted[] = $table;
                if (count($arTablesDeleted) == $countQueries)
                {
                    $doQuery       = false;
                    $deleteSuccess = true;
                    break;
                }
            }
            $lastCountQueriesErrors = $countQueriesErrors;
        }
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        return $deleteSuccess;
    }

    public function getError()
    {
        return $this->error;
    }

}
