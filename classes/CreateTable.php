<?php

class CreateTable {

    public function addGames() {
        $Database = Database::checkConnect();
        $sql = $Database->connectDatabase();

        $Database->truncateTable($sql, $table);

        $sql->close();
    }

}