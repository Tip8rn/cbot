<?php // callback.php
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "vendor/autoload.php";
require_once('setting.php');

///////////// ส่วนของการเรียกใช้งาน class ผ่าน namespace
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\AccountLinkEvent;
use LINE\LINEBot\Event\MemberJoinEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
use LINE\LINEBot\QuickReplyBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraRollTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\CameraTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\LocationTemplateActionBuilder;
use LINE\LINEBot\RichMenuBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuSizeBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBoundsBuilder;

// เชื่อมต่อกับ LINE Messaging API
$httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
$bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));
// คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
$content = file_get_contents('php://input');

// แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
$events = json_decode($content, true);
$replyData = NULL;
$replyToken = NULL;
if(!is_null($events)){
    // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
    $replyToken = $events['events'][0]['replyToken'];
    $typeMessage = $events['events'][0]['message']['type'];
    $userMessage = $events['events'][0]['message']['text'];
    $userMessage = strtolower($userMessage);
    switch ($typeMessage){
        case 'text':
            switch ($userMessage) {
                case "t":
                    $textReplyMessage = "Bot ตอบกลับคุณเป็นข้อความ";
                    $replyData = new TextMessageBuilder($textReplyMessage);
                    break;
                case "i":
                    $picFullSize = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower';
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/simpleflower/240';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;
                case "v":
                    $picThumbnail = 'https://www.mywebsite.com/imgsrc/photos/f/sampleimage/240';
                    $videoUrl = "https://www.mywebsite.com/simplevideo.mp4";
                    $replyData = new VideoMessageBuilder($videoUrl,$picThumbnail);
                    break;
                case "a":
                    $audioUrl = "https://www.mywebsite.com/simpleaudio.mp3";
                    $replyData = new AudioMessageBuilder($audioUrl,27000);
                    break;
                case "l":
                    $placeName = "ที่ตั้งร้าน";
                    $placeAddress = "แขวง พลับพลา เขต วังทองหลาง กรุงเทพมหานคร ประเทศไทย";
                    $latitude = 13.780401863217657;
                    $longitude = 100.61141967773438;
                    $replyData = new LocationMessageBuilder($placeName, $placeAddress, $latitude ,$longitude);
                    break;
                case "s":
                    $stickerID = 22;
                    $packageID = 2;
                    $replyData = new StickerMessageBuilder($packageID,$stickerID);
                    break;
                case "im":
                    $imageMapUrl = 'https://www.mywebsite.com/imgsrc/photos/w/sampleimagemap';
                    $replyData = new ImagemapMessageBuilder(
                        $imageMapUrl,
                        'This is Title',
                        new BaseSizeBuilder(699,1040),
                        array(
                            new ImagemapMessageActionBuilder(
                                'test image map',
                                new AreaBuilder(0,0,520,699)
                                ),
                            new ImagemapUriActionBuilder(
                                'http://www.ninenik.com',
                                new AreaBuilder(520,0,520,699)
                                )
                        ));
                    break;
                case "tm":
                    $replyData = new TemplateMessageBuilder('Confirm Template',
                        new ConfirmTemplateBuilder(
                                'Confirm template builder',
                                array(
                                    new MessageTemplateActionBuilder(
                                        'Yes',
                                        'Text Yes'
                                    ),
                                    new MessageTemplateActionBuilder(
                                        'No',
                                        'Text NO'
                                    )
                                )
                        )
                    );
                    break;
                    case "สถานการณ์โรค":
                        // กำหนด action 4 ปุ่ม 4 ประเภท
                        $actionBuilder1 = array(
                          // new PostbackTemplateActionBuilder(
                          //     'สถาณการณ์โรค', // ข้อความแสดงในปุ่ม
                          //     http_build_query(array(
                          //         'disease_code'=>'26,27,66',
                          //     )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                          //     'สถานการณ์-โรคไข้เลือดออก'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                          // ),
                          new UriTemplateActionBuilder(
                                'สถานการณ์โรค', // ข้อความแสดงในปุ่ม
                                'https://flu.ddc.moph.go.th/bot/chart.php?disease_code=26-27-66'
                            ),
                          new UriTemplateActionBuilder(
                                'อาการของโรค', // ข้อความแสดงในปุ่ม
                                'https://ddc.moph.go.th/th/site/disease/detail/44/symptom'
                            ),
                        );
                        $actionBuilder2 = array(
                              // new PostbackTemplateActionBuilder(
                              //     'สถาณการณ์โรค', // ข้อความแสดงในปุ่ม
                              //     http_build_query(array(
                              //         'disease_code'=>'11',
                              //     )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                              //     'สถานการณ์-โรคมือเท้าปาก'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                              // ),
                              new UriTemplateActionBuilder(
                                    'สถานการณ์โรค', // ข้อความแสดงในปุ่ม
                                    'https://flu.ddc.moph.go.th/bot/chart.php?disease_code=71'
                                ),
                              new UriTemplateActionBuilder(
                                    'อาการของโรค', // ข้อความแสดงในปุ่ม
                                    'https://ddc.moph.go.th/th/site/disease/detail/11/symptom'
                                ),
                        );
                        $actionBuilder3 = array(
                                  // new PostbackTemplateActionBuilder(
                                  //     'สถาณการณ์โรค', // ข้อความแสดงในปุ่ม
                                  //     http_build_query(array(
                                  //         'disease_code'=>'13',
                                  //     )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                  //     'สถานการณ์โรค'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                  // ),
                                  new UriTemplateActionBuilder(
                                        'สถานการณ์โรค', // ข้อความแสดงในปุ่ม
                                        'https://flu.ddc.moph.go.th/bot/chart.php?disease_code=15'
                                    ),
                                  new UriTemplateActionBuilder(
                                        'อาการของโรค', // ข้อความแสดงในปุ่ม
                                        'https://ddc.moph.go.th/th/site/disease/detail/13/symptom'
                                    ),
                        );
                        $replyData = new TemplateMessageBuilder('Carousel',
                            new CarouselTemplateBuilder(
                                array(
                                    new CarouselColumnTemplateBuilder(
                                        'โรคไข้เลือดออก',
                                        'รายละเอียด-โรคไข้เลือดออก',
                                        'https://flu.ddc.moph.go.th/image-line/dhf_c.jpg',
                                        $actionBuilder1
                                    ),
                                    new CarouselColumnTemplateBuilder(
                                        'โรคมือเท้าปาก',
                                        'รายละเอียด-โรคมือเท้าปาก',
                                        'https://flu.ddc.moph.go.th/image-line/hfm_c.jpg',
                                        $actionBuilder2
                                    ),
                                    new CarouselColumnTemplateBuilder(
                                        'โรคไข้หวัดใหญ่',
                                        'รายละเอียด-โรคไข้หวัดใหญ่',
                                        'https://flu.ddc.moph.go.th/image-line/flu_c.jpg',
                                        $actionBuilder3
                                    ),
                                )
                            )
                        );
                    break;
                default:
                    $textReplyMessage = " คุณไม่ได้พิมพ์ ค่า ตามที่กำหนด";
                    $replyData = new TextMessageBuilder($textReplyMessage);
                    break;
            }
            break;
        default:
            $textReplyMessage = json_encode($events);
            $replyData = new TextMessageBuilder($textReplyMessage);
            break;
    }
}
//l ส่วนของคำสั่งตอบกลับข้อความ
if(isset($replyToken) && $replyData){
  $response = $bot->replyMessage($replyToken,$replyData);
}

echo "OK";
