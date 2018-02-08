<?php

$url = 'https://www.livelib.ru/book/1002002378/quotes-otverzhennye-viktor-gyugo#quotes'; // ссылка для парса

// n - кол-во страниц ;
$n = 3;

function check_url($url)
{

    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

    /* Get the HTML or whatever is linked in $url. */
    $response = curl_exec($handle);

    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if ($httpCode == 404) {
        /* Handle 404 here. */
        $ret = 0;
    } else {
        $ret = 1;
    }

    curl_close($handle);
    return $ret;
}

function get_content($url)
{

    if (check_url($url) == 1) {
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); //агент которым мы представимся
        curl_setopt ($ch, CURLOPT_TIMEOUT, 15 ); // таймаут
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

        $result_body = curl_exec($ch);

        curl_close($ch);

        preg_match_all('|<div class="review-text-right">(.*?)<p>(.*?)</p>|sei', $result_body, $title);

        $content_massive = $title[0];

        return $content_massive;

    } else {
        return 0;
    }
}


function get_first_page($url)
{
    //1-я страница

    $pages_content_massive = get_content($url);
    $i = 1;
    $pages_content = "";

    foreach ($pages_content_massive as $item) {

        $pages_content .= "Title $i: $item <br>";
        $i = $i + 1;
    }

    return $pages_content;
}



function get_all_pages($url,$n)
{
    $pages_content = "<br>Цитаты: <br>";

    for ($i = 1; $i <= $n; $i++) {
        $url = mb_strimwidth($url, 0, 47, "/~");
        $url = $url . "$i";

        if (check_url($url) == 1) {

            $pages_content_massive = get_content($url);
            $j = ($i - 1) * 10 + 1;
            foreach ($pages_content_massive as $item) {
                $pages_content .= "Title $j: $item <br>";
                $j = $j + 1;
            }
        } else {
            break;
        }
    }
        return $pages_content;

}


//var_dump(get_first_page($url));
var_dump(get_all_pages($url,$n));
//echo get_all_pages($url,$n);

