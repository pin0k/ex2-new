<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->addEventHandler("main", "OnAdminContextMenuShow", [Main::class, "showDetailButton"]);

class Main {
    static function showDetailButton(&$items) {
        $request = Application::getInstance()->getContext()->getRequest();

        if('/bitrix/admin/iblock_element_edit.php' == $request->getRequestedPage() && Loader::includeModule("iblock")) {
            $elements = \CIBlockElement::GetList(
                [],
                [
                    'ID' => $request->get('ID')
                ],
                false,
                false,
                [
                    'ID',
                    'DETAIL_PAGE_URL'
                ]
            );

            if ($elem = $elements->getNext()) {
                $items[] = [
                    'TEXT' => Loc::getMessage('MY_COMPANY_CUSTOM_TO_SITE_BTN'),
                    'LINK' => $elem['DETAIL_PAGE_URL']
                ];
            }
        }
    }
}