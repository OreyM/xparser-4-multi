<?php

class InsertData {

    public function addDataTable($countryArray, $table) {
        $Database = Database::checkConnect();
        $sql = $Database->connectDatabase();

        $Database->truncateTable($sql, $table);

        $sql->query("INSERT INTO {$table} (game_id, game_name, game_link_usa_en_us, game_price_usa_en_us, before_discount_usa_en_us, discount_usa_en_us)
                     SELECT game_id, game_name, game_link, game_price, before_discount, discount
                     FROM usa_en_us");

        while ($array = current($countryArray)) {

            $nextCountryID = next($countryArray);

            if (!($nextCountryID))
                break;

            $nextCountryTable = key($countryArray);

            $sql->query("INSERT INTO {$table} (game_id, game_name)
                         SELECT {$nextCountryTable}.game_id, {$nextCountryTable}.game_name FROM {$nextCountryTable}
                         LEFT JOIN {$table} ON ({$nextCountryTable}.game_id = {$table}.game_id)
                         WHERE {$table}.game_id IS NULL
                         ");

            $sql->query("UPDATE {$table}, {$nextCountryTable}
                         SET {$table}.game_link_{$nextCountryTable} = {$nextCountryTable}.game_link, {$table}.game_price_{$nextCountryTable} = {$nextCountryTable}.game_price, 
                             {$table}.before_discount_{$nextCountryTable} = {$nextCountryTable}.before_discount, {$table}.discount_{$nextCountryTable} = {$nextCountryTable}.discount
                         WHERE {$table}.game_id = {$nextCountryTable}.game_id
                         ");

        }

        $sql->close();
    }

    public function bestPrice($table){



    }

}