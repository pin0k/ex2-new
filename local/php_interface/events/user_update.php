<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Mail\Event;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->addEventHandler("main", "OnBeforeUserUpdate", [userHandler::class, "fillOldGroups"]);
$eventManager->addEventHandler("main", "OnAfterUserUpdate", [userHandler::class, "notifyAdmins"]);

class userHandler {

	private static $oldGroups = [];
	private const ADMIN_GROUP_ID = 1;
	private const MY_SITE_ID = 's1';

	static function fillOldGroups(&$arParams) {
		$groups = UserGroupTable::getList([
			'filter' => [
				'USER_ID' => $arParams['ID'],
				
			],
			'select' => ['GROUP_ID']
		]);

		static::$oldGroups = [];

		while ($group = $groups->fetch()) {
			static::$oldGroups[] = $group['GROUP_ID'];
		}
	}

	static function notifyAdmins(&$arParams) {
		$newGroups = array_column($arParams['GROUP_ID'], 'GROUP_ID');
		$isAddedToAdmin = !in_array(self::ADMIN_GROUP_ID, static::$oldGroups) && in_array(self::ADMIN_GROUP_ID, $newGroups);

		if ($isAddedToAdmin) {
			$rsUsers = UserTable::getList([
				'filter' => [
					'GROUP_ID' => self::ADMIN_GROUP_ID,
					"!ID" => $arParams['ID']
				],
				'select' => ['EMAIL', 'GROUP_ID' => 'GROUPS.GROUP_ID']
			]);

			$adminEmails = [];
			while ($user = $rsUsers->fetch()) {
				$adminEmails[] = $user['EMAIL'];
			}

			Event::send([
				'EVENT_NAME' => 'NEW_ADMIN',
				'LID' => self::MY_SITE_ID,
				'C_FIELDS' => [
					'NAME' => $arParams['LAST_NAME']." ".$arParams['NAME'],
					'EMAIL' => $arParams['EMAIL'],
					'EMAIL_TO' => implode(',', $adminEmails)
				]
			]);
		}
	}
}