<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Mail\Event;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->addEventHandler("main", "OnBeforeUserUpdate", [userUpdateEx2600::class, "checkValueFieldsUserClass"]);
$eventManager->addEventHandler("main", "OnAfterUserUpdate", [userUpdateEx2600::class, "notifyUserUpdateClass"]);

class userUpdateEx2600 {

    private static $oldUserClass = '';
    private const MY_SITE_ID = 's1';

    public static function checkValueFieldsUserClass(&$arFields) {
        $userORM = CUser::GetList(
			'',
			'',
			[
				'ID' => $arFields['ID']
			],
			[
				'SELECT' => ['UF_USER_CLASS'],
			]
		);

        while ($arUser = $userORM->Fetch()) {
            static::$oldUserClass = $arUser['UF_USER_CLASS'];
        }
    }

    public static function notifyUserUpdateClass(&$arFields) {
        if($arFields["RESULT"]) {
            if(intval($arFields["UF_USER_CLASS"]) !== intval(static::$oldUserClass)) {

                Event::send([
                    'EVENT_NAME' => 'EX2_AUTHOR_INFO',
                    'LID' => self::MY_SITE_ID,
                    'C_FIELDS' => [
                        'OLD_USER_CLASS' => static::$oldUserClass,
                        'NEW_USER_CLASS' => $arFields["UF_USER_CLASS"],
                        'NAME' => $arFields['LAST_NAME']." ".$arFields['NAME'],
                        'EMAIL' => Loc::getMessage('EMAIL'),
                        'EMAIL_TO' => Loc::getMessage('EMAIL_TO')
                    ]
                ]);
            }
        }
        
    }
}