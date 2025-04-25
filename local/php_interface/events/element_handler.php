<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Mail\Event;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", [elementIblockHandler::class, "elementIBlockUpdate"]);
$eventManager->addEventHandler("iblock", "OnAfterIBlockElementUpdate", [elementIblockHandler::class, "updateWriteLog"]);

class elementIblockHandler {
    private const REVIEWS_IBLOCK_ID = 5;
    private const LENGTH_REVIEWS_PREVIEW_TEXT = 5;
    private const PROPERTY_AUTHOR_ID = 9;
    private const PROPERTY_AUTHOR_TYPE_ID = 148;
    protected static $oldAuthor = '';
    
	public static function elementIBlockUpdate(&$arFields) {
        if($arFields["IBLOCK_ID"] !== self::REVIEWS_IBLOCK_ID) {
            return true;
        }

        $elementsORM = CIBlockElement::GetList(
            [],
            [
                "IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]),
                'ID' => intval($arFields["ID"])
            ],
            false,
            false,
            ['PROPERTY_AUTHOR']
        );
        while($element = $elementsORM->Fetch()) {
            self::$oldAuthor = $element["PROPERTY_AUTHOR_VALUE"];            
        }

        if(mb_strlen($arFields["PREVIEW_TEXT"]) <= self::LENGTH_REVIEWS_PREVIEW_TEXT) {
            global $APPLICATION;
			$APPLICATION->throwException(Loc::getMessage("SHORT_LENGTH_PREVIEW_TEXT", [
                "#LENGTH#" => mb_strlen($arFields["PREVIEW_TEXT"])
            ]));
			return false;
        }
        if(str_contains($arFields["PREVIEW_TEXT"], '#del#')) {
            $arFields["PREVIEW_TEXT"] = str_replace($arFields["PREVIEW_TEXT"], '', $arFields["PREVIEW_TEXT"]);
        }
	}

    public static function updateWriteLog(&$arFields) {
        if($arFields["RESULT"]) {
            if($arFields["PROPERTY_VALUES"][self::PROPERTY_AUTHOR_ID][self::PROPERTY_AUTHOR_TYPE_ID]["VALUE"] != self::$oldAuthor) {
                CEventLog::Add([
                    "SEVERITY" => "INFO",
                    "AUDIT_TYPE_ID" => "ex2_590",
                    "MODULE_ID" => "iblock",
                    "DESCRIPTION" => Loc::getMessage('CHANGE_AUTHOR_REVIEWS', [
                        '#ID#' => $arFields['ID'],
                        '#AUTHOR_OLD_ID#' => self::$oldAuthor,
                        '#AUTHOR_NEW_ID#' => $arFields["PROPERTY_VALUES"][self::PROPERTY_AUTHOR_ID][self::PROPERTY_AUTHOR_TYPE_ID]["VALUE"]]),
                ]);
            }
        } else
			AddMessage2Log("Ошибка изменения записи ".$arFields["ID"]." (".$arFields["RESULT_MESSAGE"].").");
    }
}