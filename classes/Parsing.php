<?php
require_once 'phpQuery/phpQuery.php';
require_once 'MultiCurl.php';
require_once 'Curl.php';
require_once 'Database.php';

class Parsing{

    private static
        $parsingUrls = array(),
        $gamesData = array(),
        $gamesUrlsForParsing = array(),
        $imagesUrls = array();

    private
        $nextPageArray = array();

    public function __construct(){

    }

    static public function getGeneralUrls($countryID, array $sitePage){

            foreach ($sitePage as $sitePageIdentif) {
                self::$parsingUrls[] = GAME_URL . $countryID . $sitePageIdentif;
            }

            return self::$parsingUrls;
    }

    private function discountType(phpQueryObject $parsingData, $productPrice, $productBeforeDiscountPrice){

        if($productPrice === 'Free' || !empty($productBeforeDiscountPrice)){
            $discountType = $parsingData->find('.c-price span img')->attr('alt');
            if(empty($discountType)){
                $discountType = $parsingData->find('.c-price > span:last')->text();
                if(empty($discountType)){
                    $discountType = 'Discount';
                }
            }
        } else {
            $discountType = 'NONE';
        }

        if($discountType === 'Discount' && $productPrice === 'Free'){
            $discountType = 'FreeGame';
        }

        return $discountType;
    }

    public function formationParsingData(array $parsingUrls, array $pageElement, $parsNextPage = False){

        #Очищаем массив полученых ссылок на следующий парсинг
        $this->nextPageArray = array();

        #Получаем массив слепков страниц для дальнейшей обработки
        $curlData = (new MultiCurl($parsingUrls))->getData();

        foreach ($curlData as $somePage) {

            #Преобразуем полученные данные в ДОМ-структуру
            $elementParsing = phpQuery::newDocument($somePage);

            //echo $elementParsing;

            #Перебераем ДОМ-элементы
            foreach ($elementParsing->find($pageElement['fullPage']) as $parsingData) {

                $parsingData = pq($parsingData);

                $productLink = GAME_URL . ($parsingData->find('> a')->attr('href'));
                if(substr($productLink, -5) == 'chart')
                    $productLink = str_replace('?cid=msft_web_chart', '', $productLink);
                $productID = substr($productLink, -12);

                #Проверка для исключения дублирования внесения данных в массив игр
                if(!isset(self::$gamesData[$productID])){

                    $productTitle = $parsingData->find($pageElement['titleElement'])->text();
                    $productPrice = trim($parsingData->find($pageElement['priceElement'])->text());
                    $productBeforeDiscountPrice = trim($parsingData->find($pageElement['discountElement'])->text());
                    $discountType = $this->discountType($parsingData, $productPrice, $productBeforeDiscountPrice);

                    if($productPrice === $discountType)
                        $discountType = 'Discount';

                    #Вносим полученные данные в массив
                    $productArray = [
                        'game_id'       => $productID,
                        'game_name'     => $productTitle,
                        'game_link'     => $productLink,
                        'game_price'    => $productPrice,
                        'before_discount' => $productBeforeDiscountPrice,
                        'discount' => $discountType
                    ];

                    self::$gamesData[$productID] = $productArray;

                    #Собираем урлы на бесплатные игры + без картинок
                    if($productArray['game_price'] === 'Free'){
                        self::$gamesUrlsForParsing[] = $productLink;
                    }
                    $filename = 'images/game_img/'.$productID.'.jpg';
                    if (!file_exists($filename)){
                        self::$imagesUrls[] = $productLink;
                    }
                }
            }

            #Получаем ссылку на следующую страницу
            $nextPageUrl = $elementParsing->find('.m-pagination > .f-active')->next('')->find('a')->attr('href');
            #ПРоверяем что бы ссылка не заканчивалась на -1
            $checkCorrectUrl = substr($nextPageUrl, -2);
            #Если ссылка не пустая и не заканчиваеться на -1, вносим ее в массив ссылок для MultiCurl
            if(!empty($nextPageUrl) && $checkCorrectUrl != -1){
                $this->nextPageArray[] = GAME_URL . $nextPageUrl;
            }
        }
        #Если полученный массив ссылок не пустой, рекурсируем метод
        if($parsNextPage){
            if(!empty($this->nextPageArray)){
                $this->formationParsingData($this->nextPageArray, $pageElement, TRUE);
            }
        }
    }

    # array some Games for parsing[string Games ID] = [
    #     Games url => string, 'gameLink'
    #     Images check => bool 'imageCheck'
    # ];

    # $parsingResult = [
    # 'gamesData' => self::$gamesData,
    # 'someGameUrl' => $gamesUrlsForParsing
    # ];
    public function parsingSomeGames(array $gamePageElements, $iterations){

        for($i = 0; $i < $iterations; ++$i){
            if(!empty(self::$gamesUrlsForParsing))
                $curlUrls[] = array_shift(self::$gamesUrlsForParsing);
            else
                break;
        }

        if(!is_null($curlUrls)){

            $curlData = (new MultiCurl($curlUrls))->getData();

            foreach ($curlData as $url => $somePage){
                $elementParsing = phpQuery::newDocument($somePage);
                $gameID = substr($url, -12);

                foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {

                    $parsingData = pq($parsingData);
                    $parsingData->find($gamePageElements['realPrice'] . ' > sup')->remove();
                    $productPrice = trim($parsingData->find($gamePageElements['freeRealPrice'])->text());
                    if(empty($productPrice)){
                        $parsingData->find($gamePageElements['realPrice'] . ' > sup')->remove();
                        $productPrice = trim($parsingData->find($gamePageElements['realPrice'])->text());
                    }

                    self::$gamesData[$gameID]['before_discount'] = $productPrice;

                }
            }

            if(!empty(self::$gamesUrlsForParsing))
                $this->parsingSomeGames($gamePageElements, $iterations);
        }



//        return self::$gamesData;
    }

    public function getImages(array $gamePageElements, $iterations){

        for($i = 0; $i < $iterations; ++$i){
            if(!empty(self::$imagesUrls))
                $curlUrls[] = array_shift(self::$imagesUrls);
            else
                break;
        }

        $curlData = (new MultiCurl($curlUrls))->getData();

        foreach ($curlData as $url => $somePage){
            $elementParsing = phpQuery::newDocument($somePage);
            $gameID = substr($url, -12);

            foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {

                $parsingData = pq($parsingData);

                $imgElement = $elementParsing->find('.srv_appHeaderBoxArt > img')->attr('src');
                if(substr($imgElement, 0, 6) != 'https:')
                    $imgElement = 'https:' . $imgElement;

                $path = 'images/game_new_img/'.$gameID.'.jpg';
                file_put_contents($path, file_get_contents($imgElement));
                echo "
                    <div class='container'>
                        <div class=\"alert alert-success\" role=\"alert\">
                            A new image for 
                            <strong>" . self::$gamesData[$gameID]['game_name'] . "</strong> 
                            ---- Game ID - 
                            <strong>{$gameID}</strong> 
                            ---- 
                            <a href='{$url}'>Game Link</a>
                        </div>
                    </div>
                ";
            }
        }

        if(!empty(self::$imagesUrls))
            $this->getImages($gamePageElements, $iterations);

    }

    public function oneGame($url, array $gamePageElements){

        $gameID = substr($url, -12);


        $curlData = (new Curl($url))->getCurlData();

            #Преобразуем полученные данные в ДОМ-структуру
            $elementParsing = phpQuery::newDocument($curlData);

            foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {

                $parsingData = pq($parsingData);


                $imgElement = $elementParsing->find('.srv_appHeaderBoxArt > img')->attr('src');

                if(substr($imgElement, 0, 6) != 'https:')
                    $imgElement = 'https:' . $imgElement;

                $testImgElement = substr($imgElement, -3);

                $filename = 'images/game_img/'.$gameID.'.jpg';
                if (!file_exists($filename)){
                    $path = 'images/game_new_img/'.$gameID.'.jpg';
                    file_put_contents($path, file_get_contents($imgElement));
                }
//                echo $imgElement . '<br>';
//                echo $testImgElement . '<br>';
            }


    }

    public function transformPrice($counrtyIdentification) {

        foreach (self::$gamesData as $gameID => $dataArray){

//            echo $dataArray['game_price'] . ' ----- ' . ['before_discount'] . '<br>';

            if(!empty($dataArray['game_price']) && $dataArray['game_price'] !== 'Free'){
                $dataArray['game_price'] = htmlentities($dataArray['game_price']);
                $dataArray['game_price'] = preg_replace('/[^0-9,.]/', '', $dataArray['game_price']);
                if($counrtyIdentification === 'rus_ru_ru')
                    $dataArray['game_price'] = str_replace(',', '.', $dataArray['game_price']);
                self::$gamesData[$gameID]['game_price'] = (float)$dataArray['game_price'];
            }

            if(!empty($dataArray['before_discount']) && $dataArray['before_discount'] !== 'Free'){
                $dataArray['before_discount'] = htmlentities($dataArray['before_discount']);
                $dataArray['before_discount'] = preg_replace('/[^0-9,.]/', '', $dataArray['before_discount']);
                if($counrtyIdentification === 'rus_ru_ru')
                    $dataArray['game_price'] = str_replace(',', '.', $dataArray['game_price']);
                self::$gamesData[$gameID]['before_discount']  = (float)$dataArray['before_discount'];
            }

//            echo $dataArray['game_price'] . ' ----- ' . ['before_discount'] . '<br>';
        }

    }

    public function addDataDB($table){
        $Database = Database::checkConnect();
        $sql = $Database->connectDatabase();

        $Database->truncateTable($sql, $table);

        foreach (self::$gamesData as $toDBData) {

            $Database->insertData($sql, $table, $toDBData);

        }


        $sql->close();
    }

    public function varDump(){
        echo '<pre>';
        var_dump(self::$gamesData);
        echo '</pre>';
    }



    public function clearParcingData(){
        self::$parsingUrls = array();
        $this->nextPageArray = array();
        self::$gamesData = array();
        self::$imagesUrls = array();
    }
}