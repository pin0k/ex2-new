<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Type\DateTime as BitrixDateTime;

Loc::loadMessages(__FILE__);

class SendReviewsUpdate {

    private const REVIEWS_IBLOCK_ID = '5';

    private static $timeLastCheck = "";

	public static function Agent_ex_610($lastTimeExec = "") {
		if (Loader::includeModule("iblock")) {

			$result = static::prepareResult($lastTimeExec);

			if ($result) {
                CEventLog::Add(array(
                    "SEVERITY" => "INFO",
                    "AUDIT_TYPE_ID" => "ex2_610",
                    "MODULE_ID" => "iblock",
                    "DESCRIPTION" => Loc::getMessage("MESSAGE_LOG", [
                        "#ELEMENTS_COUNT#" => count($result),
                        "#DATA_TIME_LAST_START#" => static::$timeLastCheck,
                    ]),
                ));
			}
		}

		return "\\" . __METHOD__ . "(\"" . (new BitrixDateTime())->toString() . "\");";
	}

	protected static function prepareResult($lastTimeExec = "") {
		$query = new Query(ElementTable::getEntity());

		$query->setSelect([
			"ID",
			"NAME",
			"IBLOCK_ID",
			"IBLOCK_NAME" => "IBLOCK.NAME"
		]);
        
		$query->setFilter([
			">DATE_CREATE" => $lastTimeExec ?: (new BitrixDateTime())->add("-1 day"),
            "IBLOCK_ID" => self::REVIEWS_IBLOCK_ID,
		]);
		$query->setOrder([
			"IBLOCK_ID" => "ASC",
		]);
		$query->registerRuntimeField("IBLOCK", [
			"data_type" => IblockTable::class,
			"reference" => [
				"=this.IBLOCK_ID" => "ref.ID"
			]
		]);

        static::$timeLastCheck = $lastTimeExec ?: (new BitrixDateTime())->add("-1 day")->toString();

        return $query->exec()->fetchAll();
	}


}