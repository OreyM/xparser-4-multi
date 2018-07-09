<?php
class Curl{
    private
        $ch,
        $url;

    public function __construct($url){
        $this->url = $url;
        $this->ch = curl_init($url);
    }

    public function getCurlData() {

        //Curl settings
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:50.0) Gecko/20100101 Firefox/50.0');
        curl_setopt($this->ch, CURLOPT_REFERER, $this->url);

        $page = curl_exec($this->ch);

//        $info = curl_getinfo($this->ch);
//        echo "<br><br> Прошло {$info['total_time']} секунд во время запроса к {$info['url']} <br><br>";

        curl_close($this->ch);

        return $page;
    }
}