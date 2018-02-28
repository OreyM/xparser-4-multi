<?php
require_once 'phpQuery/phpQuery.php';
require_once 'MultiCurl.php';
require_once 'Curl.php';

class Parsing{

    private static
        $parsingUrls = array(),
        $gamesData = array(),
        $gamesUrlsForParsing = array();

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

    public function formationParsingData(array $parsingUrls, array $pageElement){

        #Очищаем массив полученых ссылок на следующий парсинг
        $this->nextPageArray = array();

        #Получаем массив слепков страниц для дальнейшей обработки
        $curlData = (new MultiCurl($parsingUrls))->getData();

        foreach ($curlData as $somePage) {

            #Преобразуем полученные данные в ДОМ-структуру
            $elementParsing = phpQuery::newDocument($somePage);

            #Перебераем ДОМ-элементы
            foreach ($elementParsing->find($pageElement['fullPage']) as $parsingData) {

                $parsingData = pq($parsingData);

                $productLink = GAME_URL . ($parsingData->find('> a')->attr('href'));
                $productID = substr($productLink, -12);

                #Проверка для исключения дублирования внесения данных в массив игр
                if(!isset(self::$gamesData[$productID])){

                    $productTitle = $parsingData->find($pageElement['titleElement'])->text();
                    $productPrice = trim($parsingData->find($pageElement['priceElement'])->text());
                    $productBeforeDiscountPrice = trim($parsingData->find($pageElement['discountElement'])->text());
                    $discountType = $this->discountType($parsingData, $productPrice, $productBeforeDiscountPrice);

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
//                    $filename = '../images/game_img/'.$productID.'.jpg';
//                    if (!file_exists($filename)){
//                        $gamesUrlsForParsing[$productID] = [
//                            'gameLink' => $productLink,
//                            'imageCheck' => TRUE
//                        ];
//                    }
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
        if(!empty($this->nextPageArray)){
//            $this->formationParsingData($this->nextPageArray, $pageElement);
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

        $count = 0;

        for($i = 0; $i < $iterations; ++$i){
            if(!empty(self::$gamesUrlsForParsing))
                $curlUrls[] = array_shift(self::$gamesUrlsForParsing);
            else
                break;
        }

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

//        return self::$gamesData;
    }

    public function getGamesData(array $gamePageElements, $count){

        $iteration = 0;

        foreach (self::$gamesUrls as $gamesID => $gameUrl) {

            if($iteration === $count)
                break;
            else{

                $curlData = (new MultiCurl(self::$gamesUrls))->getData();

                foreach ($curlData as $somePage){

                    #Преобразуем полученные данные в ДОМ-структуру
                    $elementParsing = phpQuery::newDocument($somePage);

                    #Перебераем ДОМ-элементы
                    foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {
                        $parsingData = pq($parsingData);

                        $gameTitle = $parsingData->find($gamePageElements['titleElement'])->text();
                        $gamePublisher = $parsingData->find($gamePageElements['publisherElement'])->text();
                        $gameRealPrice = $parsingData->find($gamePageElements['realPriceElement'])->text();

                        $gameDiscountPrice = $parsingData->find($gamePageElements['discountPriceElement'])->text();
                        $gameDiscountType = $parsingData->find($gamePageElements['discountTypeElement'])->text();

                        echo "$gameTitle $gamePublisher $gameRealPrice $gameDiscountPrice $gameDiscountType <br>";
                    }

                }

                unset(self::$gamesUrls[$gamesID]);
                ++$iteration;
            }
        }

        echo count(self::$gamesUrls) . '<br>';

        if(!empty(self::$gamesUrls))
            $this->getGamesData($count);

    }

    public function someGameParsing($url, array $gamePageElements){
        $curlData = (new Curl($url))->getCurlData();

            #Преобразуем полученные данные в ДОМ-структуру
            $elementParsing = phpQuery::newDocument($curlData);

            foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {

                $parsingData = pq($parsingData);

                $gameRealPrice = $parsingData->find($gamePageElements['realPriceElement'])->text();
                $gameDiscountPrice = $parsingData->find($gamePageElements['discountPriceElement'])->text();
                $gameDiscountType = $parsingData->find($gamePageElements['discountTypeElement'])->text();

                $imgElement = $elementParsing->find('.srv_appHeaderBoxArt > img')->attr('src');
                $testImgElement = substr($imgElement, -3);

                echo $gameRealPrice . '<br>';
                echo $gameDiscountPrice . '<br>';
                echo $gameDiscountType . '<br>';
                echo $imgElement . '<br>';
                echo $testImgElement . '<br>';

//                $gameTitle = $parsingData->find($pageElement['titleElement'])->text();
//                $gamePublisher = $parsingData->find($pageElement['publisherElement'])->text();
//                $gameRealPrice = $parsingData->find($pageElement['realPriceElement'])->text();
//
//                $gameDiscountPrice = $parsingData->find($pageElement['discountPriceElement'])->text();
//                $gameDiscountType = $parsingData->find($pageElement['discountTypeElement'])->text();
//
//                echo $gameTitle . '<br>';
//                echo $gamePublisher . '<br>';
//                echo $gameRealPrice . '<br>';
//                echo $gameDiscountPrice . '<br>';
//                echo $gameDiscountType . '<br>';
            }


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
    }
}