<?php
require_once 'phpQuery/phpQuery.php';
require_once 'MultiCurl.php';
require_once 'Curl.php';

class Parsing{

    private static $parsingUrls = array();
    private
        $allParsingDataArray = array(),
        $nextPageArray = array();

    public function __construct(){

    }

    static public function getGeneralUrls($countryID, array $sitePage){


            foreach ($sitePage as $sitePageIdentif)
                self::$parsingUrls[] = GAME_URL . $countryID . $sitePageIdentif;


        return self::$parsingUrls;
    }

    public function formationParsingData(array $sitePagesReady, array $pageElement){

        #Очищаем массив полученых ссылок на следующий парсинг
        $this->nextPageArray = array();

        #Получаем массив слепков страниц для дальнейшей обработки
        $curlData = (new MultiCurl($sitePagesReady))->getData();

        foreach ($curlData as $somePage){

            #Преобразуем полученные данные в ДОМ-структуру
            $elementParsing = phpQuery::newDocument($somePage);

            #Перебераем ДОМ-элементы
            foreach ($elementParsing->find($pageElement['fullPage']) as $parsingData){

                $parsingData = pq($parsingData);

                #Получаем необходиммые данные
                $productLink = GAME_URL . ($parsingData->find('> a')->attr('href'));
                $productID = substr($productLink, -12);
                $productTitle = $parsingData->find($pageElement['titleElement'])->text();
                $productPrice = $parsingData->find($pageElement['priceElement'])->text();

                #Вносим полученные данные в массив
                $productArray = [
                    'game_id'       => $productID,
                    'game_name'     => $productTitle,
                    'game_link'     => $productLink,
                    'game_price'    => trim($productPrice),
                ];

                #Вносим массив данных в рузулютирующий массив, для дальнейшей обработки - ключ = ИД игры
                $this->allParsingDataArray[$productID] = [
                    $productArray
                ];
            }

            #Получаем ссылку на следующую страницу
            $nextPageUrl = $elementParsing->find('.m-pagination > .f-active')->next('')->find('a')->attr('href');
            #ПРоверяем что бы ссылка не заканчивалась на -1
            $checkCorrectUrl = substr($nextPageUrl, -2);

            #Если ссылка не пустая, вносим ее в массив ссылок для MultiCurl
            if(!empty($nextPageUrl) && $checkCorrectUrl != -1)
                $this->nextPageArray[] = GAME_URL . $nextPageUrl;
        }

        #Если полученный массив ссылок не пустой, рекурсируем метод
        if(!empty($this->nextPageArray)){
            $this->formationParsingData($this->nextPageArray, $pageElement);
        }

        return $this->allParsingDataArray;
    }

    public function addParsingDataDB(){
        echo '<pre>';
        var_dump($this->allParsingDataArray);
        echo '</pre>';
    }

    public function clearParcingData(){
        self::$parsingUrls = array();
        $this->nextPageArray = array();
        $this->allParsingDataArray = array();
    }
}