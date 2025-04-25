<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", [ElementHandler::class, "stopDeactivateNews"]);

class ElementHandler {
    private const NEWS_IBLOCK_ID = 1;
    private const NO_DEACTIVATE_NEWS_DAYS = 3;

    static function stopDeactivateNews(&$arFields) {
        global $APPLICATION;
        if(intval($arFields['IBLOCK_ID']) !== self::NEWS_IBLOCK_ID) {
            return true;
        } 
        if($arFields['ACTIVE'] == 'N') {
            $currentValues = ElementTable::getList([
                'filter' => [
                    'ID' => $arFields['ID'],
                ],
                'select' => ['ACTIVE', 'DATE_CREATE']
            ])->fetch();

            if($currentValues['ACTIVE'] == 'Y') {
               
                $createdDate = $currentValues['DATE_CREATE'];
                $currentDate = new DateTime();
                $diffDateTime = $currentDate->getDiff($createdDate);

                if($diffDateTime->d < self::NO_DEACTIVATE_NEWS_DAYS) {
                    $APPLICATION->throwException(Loc::getMessage('NO_DEACTIVATE_NEWS_DAYS', [
                        '#NO_DEACTIVATE_NEWS_DAYS#' => self::NO_DEACTIVATE_NEWS_DAYS,
                        '#DAYS_LEFT#' => $diffDateTime->d,
                    ]));
                    return false;
                }
            }
           
        }
        
    }
}