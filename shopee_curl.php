<?php
if (isset($_GET['url'])) $url_api = urldecode($_GET['url']); else exit();

echo shopee_curl($url_api);

function shopee_curl($url,$post="",$usecookie = false,$_sock = false,$timeout = false) {
    $if_none_match = shopee_if_none_match($url);
    if (!$if_none_match) return false;

    $ch = curl_init();
    if($post) {
    curl_setopt($ch, CURLOPT_POST ,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if($timeout){
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
    }
    if($_sock){
    curl_setopt($ch, CURLOPT_PROXY, $_sock);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    }
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36'); 
    if ($usecookie) { 
    curl_setopt($ch, CURLOPT_COOKIEJAR, $usecookie); 
    curl_setopt($ch, CURLOPT_COOKIEFILE, $usecookie);    
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "authority: shopee.vn",
    "accept: */*",
    "accept-language: vi,en;q=0.9,vi-VN;q=0.8,en-US;q=0.7",
    "cache-control: max-age=0",
    "referer: https://shopee.vn/",
    "sec-ch-ua: \" Not A;Brand\";v=\"99\", \"Chromium\";v=\"102\", \"Google Chrome\";v=\"102\"",
    "sec-ch-ua-mobile: ?0",
    "sec-ch-ua-platform: \"Window\"",
    "sec-fetch-dest: empty",
    "sec-fetch-mode: cors",
    "sec-fetch-site: same-origin",
    "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36",
    "x-api-source: pc",
    "x-requested-with: XMLHttpRequest",
    "x-shopee-language: vi",
    'if-none-match-: '.$if_none_match,
    'Referer: https://shopee.vn',
    'cookie: SPC_U='.rand(10000000,99999999)
    ));
    $result=curl_exec ($ch); 
    curl_close ($ch); 
    return $result; 
}

function shopee_if_none_match($url){
    $if_none_match = 0;
    $url_query = parse_url($url, PHP_URL_QUERY);
    $if_none_match = '55b03-'.md5('55b03'.md5($url_query).'55b03');
    return $if_none_match;
}
