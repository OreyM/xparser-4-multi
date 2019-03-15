<?php

class GamesData {

    private static
        $gamesPrice = array(),
        $insertArray = array();

    private function getGameName($sql, $gameID) {

        $countryTable = [
            'usa_en_us',
            'india_en_in',
            'newzeland_en_nz',
            'canada_en_ca',
            'afric_en_za',
            'rus_ru_ru',
            'evro_de_de',
            'mexico_es_mx',
            'brazil_pt_br',
            'argentina_es_ar',
            'columbia_es_co',
            'turkish_tr_tr',
            'singapore_en_sg',
            'hongkong_en_hk'
        ];

        foreach ($countryTable as $country) {
            $query = $sql->query("SELECT game_name FROM {$country} WHERE game_id = '{$gameID}'");
            $data =  $query->fetch_object();
            $gameName = $data->game_name;

            $query->free();

            if(!empty($gameName)) {
                break;
            }
        }

        return html_entity_decode(html_entity_decode($gameName));
    }

    private function checkQuery($data) {

        if(!$data) {

            $data = [
                'country' => NULL,
                'link' => NULL,
                'price' => NULL,
                'before_discount' => NULL
            ];
            return (object)$data;
        }
        else
            return $data->fetch_object();
    }

    public function gamesID ($countryArray) {

        $Database = Database::checkConnect();
        $sql = $Database->connectDatabase();

        $array = array();

        #Получаем массив ИД всех игр
        foreach ($countryArray as $countryTable => $countryUrl) {

            $resultQuery = $sql->query("SELECT game_id FROM {$countryTable}");

            while ($queryData = $resultQuery->fetch_object()){

                if(!array_search($queryData->game_id, self::$gamesPrice))
                    self::$gamesPrice[$queryData->game_id] = $array;
            }

            $resultQuery->free();
        }

        #Добавляем в массив self::$gamesPrice массив цен на игры
        foreach (self::$gamesPrice as $gameID => &$priceArray) {

            foreach ($countryArray as $countryTable => $countryUrl) {
                $resultQuery = $sql->query("SELECT game_price FROM {$countryTable} WHERE game_id = '{$gameID}'");
                $getPrice = $resultQuery->fetch_object();
                $price = $getPrice->game_price;

                if($getPrice->game_price == 99999.99) {
                    $resultQuery->free();
                    $resultQuery = $sql->query("SELECT before_discount FROM {$countryTable} WHERE game_id = '{$gameID}'");
                    $getPrice = $resultQuery->fetch_object();
                    $price = $getPrice->before_discount;
                }

                if(!is_null($getPrice->game_price))
                    $priceArray[$countryTable] = (float)$price;
                else
                    $priceArray[$countryTable] = $price;

                $resultQuery->free();
            }
        }

        foreach (self::$gamesPrice as $gameID => &$priceArray) {

            foreach ($priceArray as $key => $value) {
                if(is_null($value))
                    unset($priceArray[$key]);
            }
            asort($priceArray);

            if(count($priceArray) > 6){
                $priceArray = array_slice($priceArray,0, 6 - count($priceArray));
            }
        }

//        echo '<pre>';
//        var_dump(self::$gamesPrice);
//        echo '</pre>';

        #bp - Best Price, g1 - next game and etc.
        $gameInsert = array();


        foreach (self::$gamesPrice as $gameID => $sortGames) {

            $countryTable = key($sortGames);
            $bestPriceData = $sql->query("SELECT * FROM {$countryTable} WHERE game_id = '{$gameID}'")->fetch_object();
            next($sortGames);
            $countryTable = key($sortGames);
            $g1Data = $this->checkQuery($sql->query("SELECT * FROM {$countryTable} WHERE game_id = '{$gameID}'"));
            next($sortGames);
            $countryTable = key($sortGames);
            $g2Data = $this->checkQuery($sql->query("SELECT * FROM {$countryTable} WHERE game_id = '{$gameID}'"));
            next($sortGames);
            $countryTable = key($sortGames);
            $g3Data = $this->checkQuery($sql->query("SELECT * FROM {$countryTable} WHERE game_id = '{$gameID}'"));
            next($sortGames);
            $countryTable = key($sortGames);
            $g4Data = $this->checkQuery($sql->query("SELECT * FROM {$countryTable} WHERE game_id = '{$gameID}'"));
            next($sortGames);
            $countryTable = key($sortGames);
            $g5Data = $this->checkQuery($sql->query("SELECT * FROM {$countryTable} WHERE game_id = '{$gameID}'"));

            $gameInsert[$gameID] = [
                'game_id' => $gameID,
                'game_name' => $this->getGameName($sql, $gameID),
                'discount' => $bestPriceData->discount,
                'bp_country' => $bestPriceData->country,
                'bp_link' => $bestPriceData->game_link,
                'bp_price' => $bestPriceData->game_price,
                'bp_before_discount' => $bestPriceData->before_discount,
                'g1_country' => $g1Data->country,
                'g1_link' => $g1Data->game_link,
                'g1_price' => $g1Data->game_price,
                'g1_before_discount' => $g1Data->before_discount,
                'g2_country' => $g2Data->country,
                'g2_link' => $g2Data->game_link,
                'g2_price' => $g2Data->game_price,
                'g2_before_discount' => $g2Data->before_discount,
                'g3_country' => $g3Data->country,
                'g3_link' => $g3Data->game_link,
                'g3_price' => $g3Data->game_price,
                'g3_before_discount' => $g3Data->before_discount,
                'g4_country' => $g4Data->country,
                'g4_link' => $g4Data->game_link,
                'g4_price' => $g4Data->game_price,
                'g4_before_discount' => $g4Data->before_discount,
                'g5_country' => $g4Data->country,
                'g5_link' => $g4Data->game_link,
                'g5_price' => $g4Data->game_price,
                'g5_before_discount' => $g4Data->before_discount
            ];
        }

        foreach ($gameInsert as $newGameID => $newGameData) {

//            var_dump($newGameData);

            if($newGameData['bp_country'] == 'Турция') {

                if ( $newDiscount = $sql->query("SELECT discount FROM usa_en_us WHERE game_id = '{$newGameID}'") ) {
                    $newDiscount = $newDiscount->fetch_object();
                    $newDiscount = $newDiscount->discount;
                    if (!empty($newDiscount) ) {
                        $gameInsert[$newGameID]['discount'] = $newDiscount;
                    } else {
                        if ( $newGameData['discount'] == 'D&acirc;hi' ) {
                            $gameInsert[$newGameID]['discount'] = 'EA Access';
                        } else {
                            $gameInsert[$newGameID]['discount'] = $newGameData['discount'];
                        }

                    }

                } else {
//                    $newDiscount = $newGameData['discount'];
//                    echo $newGameData['discount'];
//                    $gameInsert[$newGameID]['discount'] = $newGameData['discount'];
                }
//                elseif ( $newDiscount = $sql->query("SELECT discount FROM rus_ru_ru WHERE game_id = '{$newGameID}'") ) {
//                    $newDiscount = $newDiscount->fetch_object();
//                }
//                elseif ( $newDiscount = $sql->query("SELECT discount FROM argentina_es_ar WHERE game_id = '{$newGameID}'") ) {
//                    $newDiscount = $newDiscount->fetch_object();
//                }
//                $newDiscount = $sql->query("SELECT discount FROM usa_en_us WHERE game_id = '{$newGameID}'");

//                $discount = $newDiscount->discount;

                echo ' ' .$gameInsert[$newGameID]['game_name'] . ' ' . '<strong>' . $gameInsert[$newGameID]['discount'] . '</strong><br>';


//                if( $newDiscount) {
//                    $newDiscount = $newDiscount->fetch_object();
//                    echo ' ' .$gameInsert[$newGameID]['game_name'] . ' ' . $newDiscount->discount . '<br>';
//                    $gameInsert[$newGameID]['discount'] = $newDiscount->discount;
//                } else {
//                    $newDiscount = $Database->selectData($sql, 'rus_ru_ru', 'discount')->fetch_object();
//                    $gameInsert[$newGameID]['discount'] = $newDiscount->discount;
//                }
            }
        }

        $Database->truncateTable($sql, 'ready');
        foreach ($gameInsert as $gameData) {

            $Database->insertData($sql, 'ready', $gameData);

        }

//        echo '<pre>';
//        var_dump($gameInsert);
//        echo '</pre>';

        $sql->close();

    }
}