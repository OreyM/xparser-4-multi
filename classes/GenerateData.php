<?php
//require_once 'Database.php';

class GenerateData extends Parsing {

    private static $missGamesUrls = array();

    public function checkMissGames_General($countryArray){

        $generalCountryTable = key($countryArray);
        $generalCountryID = current($countryArray);

        while ($array = current($countryArray)) {

            $nextCountryID = next($countryArray);

            if(!($nextCountryID))
                break;

            $nextCountryTable = key($countryArray);

            #$generalCountryTable ($generalCountryID)  ------  $nextCountryTable ($nextCountryID)
            #usa_en_us (/en-us) ------ rus_ru_ru (/ru-ru)

            $Database = Database::checkConnect();
            $sql = $Database->connectDatabase();

            $result = $Database->leftJoin($sql, $generalCountryTable, $nextCountryTable, 'game_id');

            $this->getGamesUrls_General($result);

            $sql->close();
        }
    }

    public function parsingMissGame(array $gamePageElements, $countryIdentification, $iterations){

        for($i = 0; $i < $iterations; ++$i){
            if(!empty(self::$missGamesUrls))
                $curlUrls[] = array_shift(self::$missGamesUrls);
            else
                break;
        }

        $curlData = (new MultiCurl($curlUrls))->getData();

        foreach ($curlData as $url => $somePage) {

            $elementParsing = phpQuery::newDocument($somePage);
            $gameID = substr($url, -12);

            #Переменная для проверки есть ли страница с игрой
            $checkGame = FALSE;

            foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {

                #Если игра есть в этом регионе, то цикл запустился, если нет, то ниже IF условие
                $checkGame = TRUE;

                $parsingData = pq($parsingData);

                $parsingData->find('.price-info > .c-price > .srv_price > span > sup')->remove();
                $realPrice = $this->transformPrice(trim($parsingData->find('.price-info > .c-price > .srv_price > span')->text()),
                                                        $countryIdentification);
                $beforeDiscountPrice = NULL;

                if(empty($realPrice)) {
                    self::$gamesData[$gameID]['game_price'] = NULL;
                    self::$gamesData[$gameID]['game_link'] = NULL;
                }
                else
                    self::$gamesData[$gameID]['game_price'] = $realPrice;
                self::$gamesData[$gameID]['before_discount'] = $beforeDiscountPrice;
            }

            if(!$checkGame){
                self::$gamesData[$gameID]['game_link'] = NULL;
            }
        }

        if(!empty(self::$missGamesUrls))
            $this->parsingMissGame($gamePageElements, $countryIdentification, $iterations);
    }

    private function getGamesUrls_General(mysqli_result $data){

        while ($missGameLink = $data->fetch_object()){

            $missGameLink->game_link = str_replace('/ru-ru', '/en-us', $missGameLink->game_link);

            $productArray = [
                'game_id'       => $missGameLink->game_id,
                'game_name'     => $missGameLink->game_name,
                'game_link'     => $missGameLink->game_link,
                'game_price'    => NULL,
                'before_discount' => NULL,
                'discount' => $missGameLink->discount
            ];
            self::$gamesData[$missGameLink->game_id] = $productArray;

            self::$missGamesUrls[] = $missGameLink->game_link;

//            echo "<a href='$missGameLink->game_link'>$missGameLink->game_name</a><br>";
        }

        echo '<pre>';
//        var_dump(self::$missGamesUrls);
        echo '</pre>';
    }
}