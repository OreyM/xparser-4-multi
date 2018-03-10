<?php

class GamesData {

    private static
        $gamesID = array(),
        $sortPrice = array();

    private function getCountryName($countryTableID) {
        switch ($countryTableID) {
            case 'usa_en_us':
                return 'США';
            case 'rus_ru_ru':
                return 'Россия';
            case 'evro_de_de':
                return 'Евро';
            case 'argentina_es_ar':
                return 'Аргентина';
            case 'brazil_pt_br':
                return 'Бразилия';
            case 'canada_en_ca':
                return 'Канада';
            case 'columbia_es_co':
                return 'Колумбия';
            case 'hongkong_en_hk':
                return 'Гонконг';
            case 'india_en_in':
                return 'Индия';
            case 'africa_en_za':
                return 'Африка';
            case 'turkish_tr_tr':
                return 'Турция';
            case 'singapore_en_sg':
                return 'Сингапур';
            case 'mexico_es_mx':
                return 'Мексика';
            case 'newzeland_en_nz':
                return 'Новая Зеландия';
        }

    }

    public function gamesID ($countryArray) {

        $Database = Database::checkConnect();
        $sql = $Database->connectDatabase();

        $array = array();

        foreach ($countryArray as $countryTable => $countryUrl) {

            $resultQuery = $sql->query("SELECT game_id FROM {$countryTable}");

            while ($queryData = $resultQuery->fetch_object()){

                if(!array_search($queryData->game_id, self::$gamesID))
                    self::$gamesID[$queryData->game_id] = $array;
            }

            $resultQuery->free();
        }

        foreach (self::$gamesID as $gameID => &$priceArray) {

            foreach ($countryArray as $countryTable => $countryUrl) {
                $resultQuery = $sql->query("SELECT game_price FROM {$countryTable} WHERE game_id = '{$gameID}'");
                $getPrice = $resultQuery->fetch_object();
                if(!is_null($getPrice->game_price))
                    $priceArray[$this->getCountryName($countryTable)] = (float)$getPrice->game_price;
                else
                    $priceArray[$this->getCountryName($countryTable)] = $getPrice->game_price;
                $resultQuery->free();
            }
        }

        foreach (self::$gamesID as $gameID => &$priceArray) {

            foreach ($priceArray as $key => $value) {
                if(is_null($value))
                    unset($priceArray[$key]);
            }
            asort($priceArray);

            if(count($priceArray) > 5){
                $priceArray = array_slice($priceArray,0, 5 - count($priceArray));
            }
        }






        echo '<pre>';
        var_dump(self::$gamesID);
        echo '</pre>';

        $sql->close();

    }
}