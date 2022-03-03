<?php
$token = '5234948616:AAEDodKPmmzuOlShdITBu8Wb0B8W9m-dL5M';
$website = 'https://api.telegram.org/bot'.$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];
$reply=$update['message']['reply_to_message']['text'];
$replay=explode(" ",$reply);

if(empty($reply)){
switch($message) {
        case '/start':
        $keyboard = array('keyboard' =>
            array(array(
                array('text'=>'/noticias','callback_data'=>"1"),
            ),
                array(
                    array('text'=>'/help','callback_data'=>"4")
                )), 'one_time_keyboard' => false, 'resize_keyboard' => true
        );
        file_get_contents('https://api.telegram.org/bot5234948616:AAEDodKPmmzuOlShdITBu8Wb0B8W9m-dL5M/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&reply_markup='.json_encode($keyboard).'&text=Elija que desea hacer');
         
        break;
        case '/noticias':
            $response = '¿De que deporte quieres?';
            sendMessage($chatId, $response,True);
            break; 
        case '/help':
            $response = 'No te ayudo';
            sendMessage($chatId, $response,false);
            break;        
    default:
        $response = 'No te he entendido';
        sendMessage($chatId, $response);
        break;
}
}
else{
    switch($message) {
        case 'ciclismo':
            getNoticias($chatId,1);
            break;
        case 'hockey':
            getNoticias($chatId,2);
            break; 
            case 'nba':
            getNoticias($chatId,3);
            break; 
        case 'formula 1':
            getNoticias($chatId,4);
                    break; 
            case 'tenis':
            getNoticias($chatId,5);
             break;                  
        default:
        $response = 'No te he entendido';
        sendMessage($chatId, $response,true);
        break;   
    }

}

function sendMessage($chatId, $response, $reply) {
    if($reply==TRUE){
        $reply_mark=array('force_reply'=>True);
        $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&reply_markup='.json_encode($reply_mark).'&text='.urlencode($response);
    }
    else $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}

function getNoticias($chatId,$periodico){  
 
    $context = stream_context_create(array('http' =>  array('header' => 'Accept: application/xml')));
    switch($periodico){
        case 1:
        $url = "https://www.sport.es/es/rss/ciclismo/rss.xml";
        break;
        case 2:
        $url = "https://www.sport.es/es/rss/hockey/rss.xml";
        break;
        case 3:
        $url = "https://www.sport.es/es/rss/nba/rss.xml";
        break;
        case 4:
        $url = "https://www.sport.es/es/rss/formula1/rss.xml";
        break;
        case 5:
        $url = "https://www.sport.es/es/rss/tenis/rss.xml";
        break ;
    }
    $xmlstring = file_get_contents($url, false, $context);
 
    $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);
 
    for($i=0;$i<10;$i++){
        $titulos = $titulos."\n\n".$array['channel']['item'][0]['title'].$array['channel']['item'][$i]['link'];
        
}
sendMessage($chatId, $titulos,false);
}


?>