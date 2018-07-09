<?php

class MissGames extends Parsing{

    public function checkMissGames(){

        $Database = Database::checkConnect();
        $sql = $Database->connectDatabase();

        $Database->truncateTable($sql, 'miss_game');

        $result = $sql->query("INSERT INTO miss_game (game_id, game_name, game_link_en_us, game_price_en_us, before_discount_en_us, discount_en_us)
                               SELECT game_id, game_name, game_link, game_price, before_discount, discount
                               FROM usa_en_us");

        var_dump($result);

        $sql->close();

    }
}
