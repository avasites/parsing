<?php
/**
 * Created by PhpStorm.
 * User: zheev
 * Date: 14.01.19
 * Time: 1:29
 */
require './constants.php';

function getUrlForSend($type)
{
    return "https://api.telegram.org/bot".CHAT_BOT."/".$type;
}

function sendPhoto($url)
{
    $urlTelegram = getUrlForSend("sendPhoto");

    $image = getPhoto($url);

    if($image['filePath']){

        $post_data = array (
            "chat_id" => '@'.CHANNEL,
            "photo" => $image['url'],
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $urlTelegram);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: multipart/form-data',
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($image['filePath']));
        // указываем, что у нас POST запрос
        curl_setopt($ch, CURLOPT_POST, 1);
        // добавляем переменные
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $html = curl_exec($ch);
        curl_close($ch);
    }

}

function curlForSendMessage($text, $url)
{

    $data = array (
        "chat_id" => '@'.CHANNEL,
        "text" => $text,
        "parse_mode" => "Markdown"
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // указываем, что у нас POST запрос
    curl_setopt($ch, CURLOPT_POST, 1);
    // добавляем переменные
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $html = curl_exec($ch);
    curl_close($ch);

}


function sendMessage($url)
{

    $data = getTextNote($url);

    $text = '';

    if($data['text'])
    {
        $text = $data['text'];
    }

    if(strlen($text) > 4096)
    {
        $text = wordwrap($text, 4095, "||");
        $text = explode('||' ,$text);
    }

    $urlTelegram = getUrlForSend("sendMessage");

    if(is_array($text))
    {
        foreach ($text as $item)
        {
            $item = mb_convert_encoding($item, 'utf-8', mb_detect_encoding($item));
            curlForSendMessage($item, $urlTelegram);
        }
    }else{

        $text = mb_convert_encoding($text, 'utf-8', mb_detect_encoding($text));
        curlForSendMessage($text, $urlTelegram);

    }


    if($data['photo'])
    {
        foreach ($data['photo'] as $photo)
        {
            sendPhoto($photo);
        }
    }

}