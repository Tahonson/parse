<?php


// в случае если понадобится на одну страницу - вот алгоритм ; в get_all_pages - этот алгоритм уже есть
$input_page = "https://www.livelib.ru/book/1002730309-stigmalion-kristina-stark";
preg_match_all('|https:..www.livelib.ru.book.*?-(.*)|', $input_page, $lib);
$input_page = mb_strimwidth($input_page, 0, 46, "/quotes-");

$url = $input_page . $lib[1][0] . "#quotes";

// n - кол-во страниц ;
$n = 3;


class Pages
{
//информация с 1 страницы
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
            preg_match_all('|<div class="review-text-right">.*?<p>(.*?) <div .*?>|sei', $result_body, $title);


            $item['name'] = $name[0];
            $item['author'] = $author[0];

            $count = count($item['author']);
            $picture_1 = $picture[1][0];

            for ($i = 0; $i <= $count; $i++) {
                $item['picture'][$i] = $picture_1;
            }

            $item['title'] = $title[0];


            $content_massive[] = $item;

            return $content_massive;

        } else {
            return 0;
        }
    }

// информация из страницы с книгой ( все цитаты постранично )
    static function get_all_pages($url, $n)
    {
        $pages_content = [];

        for ($i = 1; $i <= $n; $i++) {
            $url = mb_strimwidth($url, 0, 47, "/~");
            $url = $url . "$i";

            $pages_content_massive = Pages::get_content($url);
            if ($pages_content_massive != 0) {

                $pages_content[] = $pages_content_massive;

            } else {
                break;
            }

        }
        return $pages_content;
    }

// информация из страницы с цитатой
    static function get_page_info($url)
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
            preg_match_all('||sei', $result_body, $picture);
            //название книги
            preg_match_all('|<a class="book-name" href=".*?" title=".*?">(.*?)<.a>|sei', $result_body, $name);
            //автор книги
            preg_match_all('|<a class="book-author" href=".*?" title=".*?">(.*?)<.a>|sei', $result_body, $author);
            //цитата
            preg_match_all('|<div class="review-text-right">.*?<p>(.*?) <div .*?>|sei', $result_body, $title);


            $item['name'] = $name[0];
            $item['author'] = $author[0];
            $item['picture'] = $picture[1][0];
            $item['title'] = $title[0];

            $content_massive[] = $item;

            return $content_massive;

        } else {
            return 0;
        }

    }
// страницы цитат автора
    static function get_author_page($url, $n)
    {
        $pages_content = [];

        for ($i = 1; $i <= $n; $i++) {
            $url = mb_strimwidth($url, 0, 45, "/~");
            $url = $url . "$i";

            $pages_content_massive = Pages::get_content($url);
            if ($pages_content_massive != 0) {

                $pages_content[] = $pages_content_massive;

            } else {
                break;
            }

        }
        return $pages_content;

    }

}


//var_dump(get_first_page($url));

//echo get_all_pages($url,$n);

// <a class="right object-link" href="/quote/87-tri-tovarischa-e-m-remark" title="Перейти к цитате">

//print_r(Pages::get_all_pages($url, $n));
//var_dump(Pages::get_author_page($url,$n));

