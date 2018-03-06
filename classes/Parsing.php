<?php
require_once 'phpQuery/phpQuery.php';
require_once 'MultiCurl.php';
require_once 'Curl.php';
require_once 'Database.php';

class Parsing{

    private static
        $currencyData = array(),
        $parsingUrls = array(),
        $gamesUrlsForParsing = array(),
        $imagesUrls = array();

    protected static
        $gamesData = array();

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

    private function discountType(phpQueryObject $parsingData, $productPrice, $productBeforeDiscountPrice, $countryIdentification){

        if($productPrice === 9999999 || !empty($productBeforeDiscountPrice)){
            $discountType = $parsingData->find('.c-price span img')->attr('alt');
            if(empty($discountType)){
                $discountType = trim($parsingData->find('.c-price > span:last')->text());
                if(stristr($discountType, 'Ücretsiz') || stristr($discountType, '₺') || stristr($discountType, 'esetén') ||
                    stristr($discountType, '적용)'))
                    $discountType = 'EA Access';
                if(empty($discountType))
                    $discountType = 'Discount';
            }
        } else {
            $discountType = 'NONE';
        }

        $checkDiscount = $this->transformPrice(trim($discountType), $countryIdentification);

        if($productPrice === $checkDiscount)
            $discountType = 'Discount';

        return $discountType;
    }

    protected function transformPrice($price, $countryIdentification){

        $price = htmlentities($price);
        $price = preg_replace('/[^0-9,.]/', '', $price);

        if($countryIdentification === 'rus_ru_ru' || $countryIdentification === 'evro_de_de' || $countryIdentification === 'brazil_pt_br' ||
           $countryIdentification === 'africa_en_za' || $countryIdentification === 'turkish_tr_tr' || $countryIdentification === 'norvay_nb_no')
            $price = str_replace(',', '.', $price);

        if($countryIdentification === 'argentina_es_ar' || $countryIdentification === 'columbia_es_co'){
            $price = str_replace('.', '', $price);
            $price = str_replace(',', '.', $price);
        }

        if($countryIdentification === 'hongkong_en_hk' || $countryIdentification === 'india_en_in' || $countryIdentification === 'mexico_es_mx' ||
           $countryIdentification === 'japan_ja_jp' || $countryIdentification === 'korea_ko_kr' || $countryIdentification === 'taiwann_zh_tw') {
            $price = str_replace(',', '', $price);
        }

        $cleanPrice = (float)$price;

        return $cleanPrice;
    }

    public function currencyParsing($url) {

        $curlData = (new Curl($url))->getCurlData();

        $elementParsing = phpQuery::newDocument($curlData);

        $currency = pq($elementParsing->find('.fx-top'));

        $currencyData = array(
            'rus_ru_ru'         => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(18) td:eq(4)')->text(),
            'evro_de_de'        => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(6) td:eq(4)')->text(),
            'argentina_es_ar'   => (float)$currency->find('.pure-table:eq(0) tbody tr:eq(0) td:eq(4)')->text(),
            'brazil_pt_br'      => (float)$currency->find('.pure-table:eq(0) tbody tr:eq(7) td:eq(4)')->text(),
            'canada_en_ca'      => (float)$currency->find('.pure-table:eq(0) tbody tr:eq(8) td:eq(4)')->text(),
            'columbia_es_co'    => (float)$currency->find('.pure-table:eq(0) tbody tr:eq(11) td:eq(4)')->text(),
            'hongkong_en_hk'    => (float)$currency->find('.pure-table:eq(1) tbody tr:eq(6) td:eq(4)')->text(),
            'india_en_in'       => (float)$currency->find('.pure-table:eq(1) tbody tr:eq(8) td:eq(4)')->text(),
            'africa_en_za'      => (float)$currency->find('.pure-table:eq(4) tbody tr:eq(27) td:eq(4)')->text(),
            'turkish_tr_tr'     => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(22) td:eq(4)')->text(),
            'singapore_en_sg'   => (float)$currency->find('.pure-table:eq(1) tbody tr:eq(23) td:eq(4)')->text(),
            'japan_ja_jp'       => (float)$currency->find('.pure-table:eq(1) tbody tr:eq(9) td:eq(4)')->text(),
            'korea_ko_kr'       => (float)$currency->find('.pure-table:eq(1) tbody tr:eq(10) td:eq(4)')->text(),
            'EN_AU'             => (float)$currency->find('.pure-table:eq(3) tbody tr:eq(0) td:eq(4)')->text(),
            'mexico_es_mx'      => (float)$currency->find('.pure-table:eq(0) tbody tr:eq(22) td:eq(4)')->text(),
            'newzeland_en_nz'   => (float)$currency->find('.pure-table:eq(3) tbody tr:eq(10) td:eq(4)')->text(),
//            'ES_CL' => (float)$currency->find('.pure-table:eq(0) tbody tr:eq(10) td:eq(4)')->text(),
//            'CS_CZ' => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(5) td:eq(4)')->text(),
//            'DA_DK' => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(7) td:eq(4)')->text(),
//            'EN_GB' => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(1) td:eq(4)')->text(),
//            'HU_HU' => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(9) td:eq(4)')->text(),
//            'EN_IL' => (float)$currency->find('.pure-table:eq(3) tbody tr:eq(5) td:eq(4)')->text(),
//            'EN_NZ' => (float)$currency->find('.pure-table:eq(3) tbody tr:eq(10) td:eq(4)')->text(),
//            'NB_NO' => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(15) td:eq(4)')->text(),
//            'PL_PL' => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(16) td:eq(4)')->text(),
//            'DE_CH' => (float)$currency->find('.pure-table:eq(2) tbody tr:eq(19) td:eq(4)')->text(),
//            'ZH_TW' => (float)$currency->find('.pure-table:eq(1) tbody tr:eq(27) td:eq(4)')->text()
        );


        self::$currencyData = $currencyData;
    }

    public function formationParsingData(array $parsingUrls, array $pageElement, $countryIdentification, $parsNextPage = False){

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
                    $productPrice = $this->transformPrice(trim($parsingData->find($pageElement['priceElement'])->text()), $countryIdentification);
                    $productBeforeDiscountPrice = $this->transformPrice(trim($parsingData->find($pageElement['discountElement'])->text()), $countryIdentification);
                    if($productPrice == 0)
                        $productPrice = 9999999;
                    $discountType = $this->discountType($parsingData, $productPrice, $productBeforeDiscountPrice, $countryIdentification);

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
                    if($productArray['game_price'] === 9999999){
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
                $this->formationParsingData($this->nextPageArray, $pageElement, $countryIdentification, TRUE);
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
    public function parsingSomeGames(array $gamePageElements, $countryIdentification, $iterations){

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
                    $productPrice = $this->transformPrice(trim($parsingData->find($gamePageElements['freeRealPrice'])->text()), $countryIdentification);
                    if(empty($productPrice)){
                        $parsingData->find($gamePageElements['realPrice'] . ' > sup')->remove();
                        $productPrice = $this->transformPrice(trim($parsingData->find($gamePageElements['realPrice'])->text()), $countryIdentification);
                    }

                    if($productPrice == 0)
                        $productPrice = 9999999;

                    self::$gamesData[$gameID]['before_discount'] = $productPrice;
                    if(self::$gamesData[$gameID]['game_price'] === self::$gamesData[$gameID]['before_discount'])
                        self::$gamesData[$gameID]['discount'] = 'FreeGame';
                }
            }

        if(!empty(self::$gamesUrlsForParsing))
            $this->parsingSomeGames($gamePageElements, $countryIdentification, $iterations);
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

    public function currencyPrice($countryIdentification) {

        if($countryIdentification !== 'usa_en_us') {

            foreach (self::$gamesData as $key => $gameData) {

//                echo $gameData['game_price'] . '*' . self::$currencyData[$countryIdentification] . '<br>';

                    if($gameData['game_price'] !== 9999999)
                        self::$gamesData[$key]['game_price'] = $gameData['game_price'] * self::$currencyData[$countryIdentification];
                    if($gameData['before_discount'] !== 9999999)
                        self::$gamesData[$key]['before_discount'] = $gameData['before_discount'] * self::$currencyData[$countryIdentification];

            }
        }

    }

    public function oneGame($url, array $gamePageElements){

        $gameID = substr($url, -12);


        $curlData = (new Curl($url))->getCurlData();

            #Преобразуем полученные данные в ДОМ-структуру
            $elementParsing = phpQuery::newDocument($curlData);

//            var_dump($elementParsing->find($gamePageElements['fullPage']));

            foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {

                $check = TRUE;

                $parsingData = pq($parsingData);

                $parsingData->find('.price-info > .c-price > .srv_price > span > sup')->remove();
                $realPrice = trim($parsingData->find('.price-info > .c-price > .srv_price > span')->text());

                var_dump($realPrice);
                echo '<br>';

//                $parsingData->find($gamePageElements['realPrice'] . ' > sup')->remove();
//                $productPrice = trim($parsingData->find($gamePageElements['freeRealPrice'])->text());
//                if(empty($productPrice)){
//                    $parsingData->find($gamePageElements['realPrice'] . ' > sup')->remove();
//                    $productPrice = $this->transformPrice(trim($parsingData->find($gamePageElements['realPrice'])->text()), $countryIdentification);
//                }
//
//                if($productPrice == 0)
//                    $productPrice = 'Free';
//
//                self::$gamesData[$gameID]['before_discount'] = $productPrice;
//                if(self::$gamesData[$gameID]['game_price'] === self::$gamesData[$gameID]['before_discount'])
//                    self::$gamesData[$gameID]['discount'] = 'FreeGame';


//                $imgElement = $elementParsing->find('.srv_appHeaderBoxArt > img')->attr('src');

//                if(substr($imgElement, 0, 6) != 'https:')
//                    $imgElement = 'https:' . $imgElement;

//                $testImgElement = substr($imgElement, -3);

//                $filename = 'images/game_img/'.$gameID.'.jpg';
//                if (!file_exists($filename)){
//                    $path = 'images/game_new_img/'.$gameID.'.jpg';
//                    file_put_contents($path, file_get_contents($imgElement));
//                }
//                echo $imgElement . '<br>';
//                echo $testImgElement . '<br>';
            }

            if(!$check){
                echo 'NOT GAME!';
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
        self::$gamesData = array();
//        self::$gamesUrlsForParsing = array();
        self::$imagesUrls = array();

        $this->nextPageArray = array();
    }
}