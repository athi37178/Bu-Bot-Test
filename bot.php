<?php
$access_token = 'LiqqHhZZ3Fejw0AYMisCCzy44E6hop5sHAkzaUYpbDdFUrg7rIQchP+Vbv1dgHI9IuY3OEJKx9AhMwq8NEOLUvI1/AR1bWg9C1/Qf9jzKISV5818paDcbZwlF7UkaGUa5G9oWUbTbmQ/RUWBkRgCqwdB04t89/1O/w1cDnyilFU=';

namespace LINE\LINEBot\KitchenSink\EventHandler\MessageHandler;

use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\KitchenSink\EventHandler;

use Predis\Client; 

class TextMessageHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var \Slim\Http\Request $logger */
    private $req;
    /** @var TextMessage $textMessage */
    private $textMessage;

    private $redis;

    /**
     * TextMessageHandler constructor.
     * @param $bot
     * @param $logger
     * @param \Slim\Http\Request $req
     * @param TextMessage $textMessage
     */
    public function __construct($bot, $logger, \Slim\Http\Request $req, TextMessage $textMessage)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->req = $req;
        $this->textMessage = $textMessage;
        $this->redis = new Client(getenv('REDIS_URL'));
    }

    public function handle()
    {
        $TEACH_SIGN = '==';
        $text = $this->textMessage->getText();
        $text = trim($text);
        # Remove ZWSP
        $text = str_replace("\xE2\x80\x8B", "", $text);
        $replyToken = $this->textMessage->getReplyToken();

        if ($text == 'บอท') {
            $this->bot->replyText($replyToken, $out =
                "ใช้ $TEACH_SIGN เพื่อสอนเราได้นะ\nเช่น สวัสดี" . $TEACH_SIGN . "สวัสดีชาวโลก");
            return true;
        }

        $sep_pos = strpos($text, $TEACH_SIGN);
        if ($sep_pos > 0) {
            $text_arr = explode($TEACH_SIGN, $text, 2);
            if (count($text_arr) == 2) {
                $this->saveResponse($text_arr[0], $text_arr[1]);
            }
            return true;
        }

        $re = $this->getResponse($text);
        $re_count = count($re);
        if ($re_count > 0) {
            // Random response.
            $randNum = rand(0, $re_count - 1);
            $response = $re[$randNum];
            $this->bot->replyText($replyToken, $response);
            return true;
        }
        return false;
    }

    private function saveResponse($keyword, $response)
    {
        $this->redis->lpush("response:$keyword", $response);
    }

    private function getResponse($keyword)
    {
        return $this->redis->lrange("response:$keyword", 0, -1);
    }
}