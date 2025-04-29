<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->addEventHandler("main", "OnEndBufferContent", "clearDebugForNonAdmins");

function clearDebugForNonAdmins (&$content) {
    global $USER;
    if(!$USER->IsAdmin()) {
        $content = preg_replace("/console\.log\(.*\)/U", "", $content );
    }
}