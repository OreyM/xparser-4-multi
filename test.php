<?php

public function oneGame($url, array $gamePageElements){

    $gameID = substr($url, -12);


    $curlData = (new Curl($url))->getCurlData();

    #Преобразуем полученные данные в ДОМ-структуру
    $elementParsing = phpQuery::newDocument($curlData);

    foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {

        $parsingData = pq($parsingData);
        echo count(self::$imagesUrls) . '<br>';

        $imgElement = $elementParsing->find('.srv_appHeaderBoxArt > img')->attr('src');
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

public function formationParsingData(array $parsingUrls, array $pageElement, array $gamePageElements){

    #Очищаем массив полученых ссылок на следующий парсинг
    $this->nextPageArray = array();

    #Получаем массив слепков страниц для дальнейшей обработки
    $curlData = (new MultiCurl($parsingUrls))->getData();

    foreach ($curlData as $somePage){

        #Преобразуем полученные данные в ДОМ-структуру
        $elementParsing = phpQuery::newDocument($somePage);

        #Перебераем ДОМ-элементы
        foreach ($elementParsing->find($pageElement['fullPage']) as $parsingData){

            $parsingData = pq($parsingData);

            #Получаем необходиммые данные
            $productLink = GAME_URL . ($parsingData->find('> a')->attr('href'));
            $productID = substr($productLink, -12);

            #Проверяем, не ли такой игры уже в массиве по ИД
            if(!isset(self::$gamesData[$productID])){
                $productTitle = $parsingData->find($pageElement['titleElement'])->text();
                $productPrice = $parsingData->find($pageElement['priceElement'])->text();
                $productBeforeDiscountPrice = $parsingData->find($pageElement['discountElement'])->text();

                #Вносим полученные данные в массив
                $productArray = [
                    'game_id'       => $productID,
                    'game_name'     => $productTitle,
                    'game_link'     => $productLink,
                    'game_price'    => trim($productPrice),
                    'before_discound' => trim($productBeforeDiscountPrice),
                    'discound' => ''
                ];

                #Собираем урлы на скидочные и бесплатные игры + без картинок
                if($productArray['game_price'] === 'Free' || !empty($productArray['before_discound'])){
                    self::$gamesUrlsForParsing[$productID] = $productLink;
                }
                    $filename = '../images/game_img/'.$productID.'.jpg';
                    if (!file_exists($filename))
                        self::$gamesUrlsForParsing[$productID] = $productLink;

                #Вносим массив данных в рузулютирующий массив, для дальнейшей обработки - ключ = ИД игры
                self::$gamesData[$productID] = $productArray;
            }
        }

        #Если собранны ссылки на отдельные игры для уточненния данных, парсим
        if(!empty(self::$gamesUrlsForParsing)) {
            $curlData = (new MultiCurl(self::$gamesUrlsForParsing))->getData();
            foreach ($curlData as $url => $somePage) {

                $elementParsing = phpQuery::newDocument($somePage);
                $gameID = substr($url, -12);

                foreach ($elementParsing->find($gamePageElements['fullPage']) as $parsingData) {

                    $parsingData = pq($parsingData);

                    $gameTitle = $parsingData->find($gamePageElements['titleElement'])->text();
                    $gameRealPrice = $parsingData->find($gamePageElements['realPriceElement'])->text();
                    $gameDiscountPrice = $parsingData->find($gamePageElements['discountPriceElement'])->text();
                    $gameDiscountType = $parsingData->find($gamePageElements['discountTypeElement'])->text();

                    self::$gamesData[$gameID] = [
                        'game_id'       => $gameID,
                        'game_name'     => $gameTitle,
                        'game_link'     => $url,
                        'game_price' => $gameDiscountPrice,
                        'before_discound' => $gameRealPrice,
                        'discound' => $gameDiscountType
                    ];

//                        $filename = '../images/game_img/' . $gameID . '.jpg';
//                        if (!file_exists($filename)) {
//                            $imgElement = $elementParsing->find($gamePageElements['imageElement'])->attr('src');
//                            $testImgElement = substr($imgElement, -3);
//
//                            if ($testImgElement === 'jpg') {
//                                $path = '../images/game_new_img/' . $gameID . '.jpg';
//                                file_put_contents($path, file_get_contents($imgElement));
//                                echo "
//                                        <div class='container'>
//                                            <div class=\"alert alert-success\" role=\"alert\">
//                                                A new image for
//                                                <strong>" . self::$gamesData[$gameID]['game_name'] . "</strong> ---- Game ID -
//                                                <strong>{$gameID}</strong> ----
//                                                <a href=\"" . self::$gamesData[$gameID]['game_link'] . "\">Game Link</a>
//                                            </div>
//                                        </div>
//                                        ";
//                            } else {
//                                echo "
//                                        <div class='container'>
//                                            <div class=\"alert alert-danger\" role=\"alert\">
//                                                WARNING! Can't get a image!
//                                                <strong>" . self::$gamesData[$gameID]['game_name'] . "</strong> ---- Game ID -
//                                                <strong>{$gameID}</strong> ----
//                                                <a href=\"" . self::$gamesData[$gameID]['game_link'] . "\">Game Link</a>
//                                            </div>
//                                        </div>
//                                     ";
//                            }
//                        }
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
    if(!empty($this->nextPageArray)){
        $this->formationParsingData($this->nextPageArray, $pageElement, $gamePageElements);
    }
}