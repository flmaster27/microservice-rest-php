<?php

namespace App\Services;

use League\Csv\Reader;

class DataService
{

    private $connection = null;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function create(array $file)
    {
        $csv = Reader::createFromPath($file['tmp_name'], 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        $columns = $csv->getHeader(); //returns the CSV header record
        $records = $csv->getRecords(); //returns all the CSV records as an Iterator object

        foreach ($columns as $index => $column) {
            if ($column == "Дата начала акции") {
                $type = 'INT';
            } else {
                $type = 'VARCHAR(255)';
            }

            if ($index === 0) {
                $primary = 'PRIMARY KEY';
            } else {
                $primary = '';
            }

            $mysqlColumns[] = "`$column` $type $primary";
        }

        $createTable = "CREATE TABLE `{$file['name']}` (" . implode(",", $mysqlColumns) . ")";

        try {
            $this->connection->exec($createTable);
        } catch (\Exception $e) {
            exit('Cannot create table or table already exists!');
        }

        foreach ($records as $record) {
            $this->insert($record, $file['name']);
        }

        $randomRowData = $this->getRandomRow($file['name']);
        return $this->toggleStatus($randomRowData, $file['name']);
    }

    public function insert($data, $table)
    {
        if ($data["Дата начала акции"]) {
            $data["Дата начала акции"] = strtotime($data["Дата начала акции"]);
        }
        $columns = '`' . implode('`,`', array_keys($data)) . '`';
        $data = '"' . implode('","', $data) . '"';
        $sql = "INSERT INTO `$table` ($columns) VALUES ($data)";
        try {
            $this->connection->query($sql);
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    public function getRandomRow($table)
    {
        $sql = "SELECT * FROM `$table` ORDER BY RAND() LIMIT 1";
        $query = $this->connection->query($sql);

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function toggleStatus($data, $table)
    {
        if (isset($data['Статус'])) {
            $data['Статус'] = ($data['Статус'] == 'Off') ? 'On' : 'Off';
            $sql = "UPDATE `$table` SET `Статус` = \"{$data['Статус']}\" WHERE `ID акции` = \"{$data['ID акции']}\"";
            $this->connection->query($sql);
        }
        return $data;
    }

    public function show($table)
    {
        $sql = "SELECT * FROM `$table`";
        try {
            $query = $this->connection->query($sql);
        } catch (\Exception $e) {
            exit("Cannot show data from $table");
        }
        return $query->fetchAll(\PDO::FETCH_ASSOC);

    }
}