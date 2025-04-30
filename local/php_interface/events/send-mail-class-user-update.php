<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Mail\Event;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 

$eventManager->addEventHandler("main", "OnAfterUserUpdate", [userUpdateEx2620::class, "sendMailEx2620"]);

class UserUpdateEx2620 {
    private const MY_SITE_ID = 's1';
    private const MESSAGE_ID = 's1';

    public static function sendMailEx2620(&$arFields) {

        echo "<pre>";
        var_dump($arFields);
        echo "</pre>";
        die();


        // Event::send([
        //     'EVENT_NAME' => 'EX2_AUTHOR_INFO',
        //     'MESSAGE_ID' => self::MESSAGE_ID,
        //     'LID' => self::MY_SITE_ID,
        //     'C_FIELDS' => [
        //         'NAME' =>,
        //         'LAST_NAME' =>,
        //         'USER_ID' =>,
        //         'STATUS' =>,
        //         'LOGIN' =>,
        //         'MESSAGE' => Loc::getMessage('DATA_USER_UPDATE_FROM_ADMIN'),
        //         'CLASS' => $arFields["UF_USER_CLASS"],
        //     ]
        // ]);

    }
}