<?php 
header('Content-type:application/json');
class Tools 
{
    private static function cURL($url,$header_arr = [])
    {
        $ch = curl_init();
        curl_setopt_array($ch, $header_arr);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    } 

    private static function decode($value){
        if(!$value){return false;}
            $key = sha1('@aj7?#QGE2bu4Pro3');
            $strLen = strlen($value);
            $keyLen = strlen($key);
            $j=0;
            $decrypttext= '';
            for ($i = 0; $i < $strLen; $i+=2) {
                $ordStr = hexdec(base_convert(strrev(substr($value,$i,2)),36,16));
                if ($j == $keyLen) { $j = 0; }
                $ordKey = ord(substr($key,$j,1));
                $j++;
                $decrypttext .= chr($ordStr - $ordKey);
            }

        return $decrypttext;
    }

    public static function get_link($hash)
    {
        $dataStream = [];
        
        $link = self::decode(base64_decode($hash));

        $data = self::cURL("http://api.bilutivi.net/api/v1?url={$link}");

        $json_data = json_decode($data,true);

        if (empty($json_data['subtitle'])) {
            $subtitle = array(
                [
                    'file'=>'http://bilutivi.net/wp-content/uploads/2020/04/default.vtt',
                    'label'=>'Default',
                    'default'=>true
                ]
            );
        }else{
            $subtitle = $json_data['subtitle'];
        }
        
        if (isset($json_data['status']) && $json_data['status']) {
           $dataStream['sources'] = $json_data['sources'];
           $dataStream['subtitle'] = $subtitle;

        }
        $jsonData = json_encode($dataStream,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        return $jsonData;
    }
}

if (isset($_GET['url'])) {
   echo Tools::get_link($_GET['url']);
}



 ?>