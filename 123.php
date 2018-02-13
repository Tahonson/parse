<?php

$url = 'https://www.livelib.ru/book/1002730309/quotes-stigmalion-kristina-stark#quotes'; // ссылка для парса

// n - кол-во страниц ;
$n = 3;

class Pages
{

    static function get_content($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); //агент которым мы представимся
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // таймаут
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result_body = curl_exec($ch);


        /* Check for 200 . */
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            /* Handle 200 not here. */
            $ret = 0;
        } else {
            $ret = 1;
        }
        curl_close($ch);

        if ($ret == 1) {

            $item = [];
            //обложка href=""
            preg_match_all('|<link rel="image_src".*? href="(.*?)".*?>|sei', $result_body, $picture);
            //название книги
            preg_match_all('|<a class="book-name" href=".*?" title=".*?">(.*?)<.a>|sei', $result_body, $name);
            //автор книги
            preg_match_all('|<a class="book-author" href=".*?" title=".*?">(.*?)<.a>|sei', $result_body, $author);
            //цитата
            preg_match_all('|<div class="review-text-right">.*?<p>(.*?)</p>|sei', $result_body, $title);


            $item['name'] = $name[0];
            $item['author'] = $author[0];

            $count = count($item['author']);
            $picture_1 = $picture[1][0];

            for ($i = 0; $i <= $count; $i++) {
                $item['picture'][$i] = $picture_1;
            }

            $item['title'] = $title[0];



            $content_massive[] = $item;
            // $content_massive = [];
            //$content_massive = $content_massive + $item;

            return $content_massive;

        } else {
            return 0;
        }
    }


    static function get_all_pages($url, $n)
    {
        $pages_content = [];

        for ($i = 1; $i <= $n; $i++) {
            $url = mb_strimwidth($url, 0, 47, "/~");
            $url = $url . "$i";

            $pages_content_massive = Pages::get_content($url);
            if ($pages_content_massive != 0) {

//                    $j = ($i - 1) * 10 + 1;
//                    foreach ($pages_content_massive as $item) {
//                        $pages_content .= "Title $j: $item <br>";
//                        $j = $j + 1;

                // тту возвращается $content_massive

               // $pages_content = $pages_content + $pages_content_massive;
                $pages_content[] = $pages_content_massive;

            } else {
                break;
            }


        }
        return $pages_content;

    }
}

//var_dump(get_first_page($url));
print_r(Pages::get_all_pages($url, $n));
//echo get_all_pages($url,$n);

// <a class="right object-link" href="/quote/87-tri-tovarischa-e-m-remark" title="Перейти к цитате">