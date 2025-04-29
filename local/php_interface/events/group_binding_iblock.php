<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\GroupTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->addEventHandler("iblock", "OnIBlockPropertyBuildList", [GroupBindingIBlock::class, "getUserTypeDescription"]);

class GroupBindingIBlock {
	const USER_TYPE = "IblockGroupBinding";
 
	public static function getUserTypeDescription(): array {
		return [ 
			"USER_TYPE_ID" => static::USER_TYPE,
			"USER_TYPE" => static::USER_TYPE,
			'CLASS_NAME' => __CLASS__,
			'DESCRIPTION' => Loc::getMessage("ACADEMY_PROPERTY_DESCRIPTION"),
			'PROPERTY_TYPE' => PropertyTable::TYPE_NUMBER,
			'ConvertToDB' => [__CLASS__, 'convertToDB'],
			'ConvertFromDB' => [__CLASS__, 'convertFromDB'],
			'GetPropertyFieldHtml' => [__CLASS__, 'getPropertyFieldHtml'],
			"GetPropertyFieldHtmlMulty" => [__CLASS__, "getPropertyFieldHtmlMulty"],
			"GetAdminListViewHTML" => [__CLASS__, "getAdminListViewHTML"],
			"GetAdminListEditHTML" => [__CLASS__, "getAdminListEditHTML"],
			"GetAdminListEditHTMLMulty" => [__CLASS__, "getAdminListEditHTMLMulty"],
			"GetAdminFilterHTML" => [__CLASS__, "getAdminFilterHTML"],
			"GetUIFilterProperty" => [__CLASS__, "getUIFilterProperty"],
		];
	}

	public static function convertToDB($arProperty, $value): array {
		return $value;
	}

	public static function convertFromDB($arProperty, $value, $format = ''): array {
		return $value;
	}

	protected static function getList(): array	{
		$groups = [];

		$query = new Query(GroupTable::getEntity());
		$query->setSelect([
			"ID",
			"NAME",
		]);
		$query->setFilter([
			"ACTIVE" => "Y"
		]);
		$result = $query->exec()->fetchAll();

		foreach ($result as $item) {
			$groups[] = [
				"ID" => $item["ID"],
				"VALUE" => $item["NAME"],
			];
		}

		return $groups;
	}

	protected static function getFieldHtml($elements, $userFieldData): string {
		$fieldHtml = "<select " . ($userFieldData["MULTIPLE"] == "Y" ? "multiple" : "") .
			" name='" . $userFieldData["FIELD_NAME"] . ($userFieldData["MULTIPLE"] == "Y" ? "[]" : "") . "'>";

		if ($userFieldData["MANDATORY"] != "Y") {
			$fieldHtml .= "<option value=''" . (empty(array_filter($elements, function ($el) {
					if ($el["SELECTED"] == "Y") {
						return true;
					}
					return false;
				})) ? " selected" : "") . ">" . htmlspecialcharsbx(static::getEmptyCaption($userFieldData)) . "</option>";
		}

		foreach ($elements as $element) {
			$fieldHtml .= "<option value='" . $element["ID"] . "'" . ($element["SELECTED"] == "Y" ? " selected" : "") . ">" . $element["VALUE"]
				. "</option>";
		}

		$fieldHtml .= "</select>";

		return $fieldHtml;
	}

	protected static function getEmptyCaption($arUserField): string {
		return $arUserField["SETTINGS"]["CAPTION_NO_VALUE"] <> ''
			? $arUserField["SETTINGS"]["CAPTION_NO_VALUE"]
			: Loc::getMessage("ACADEMY_GROUP_NOT_SELECTED");
	}

	public static function getPropertyFieldHtml($userField, $value, $additionalParameters): string {
		$groups = static::getList();
		if(!$groups) {
			return "";
		}
		$groups = array_map(function($el) use ($userField, $value) {
			if(!$value)
				$el["SELECTED"] = "N";
			else {
				if($el["ID"] == $value["VALUE"])
					$el["SELECTED"] = "Y";
				else
					$el["SELECTED"] = "N";
 			}
			return $el;
		}, $groups); 
		return static::getFieldHtml($groups, ["FIELD_NAME" => $additionalParameters["VALUE"]] + $userField);
	}

	public static function getPropertyFieldHtmlMulty(array $userField, $value, array $additionalParameters): string {
		$groups = static::getList();
		if (!$groups) {
			return "";
		}

		$groups = array_map(function ($el) use ($userField, $value) {
			if (!$value)
				$el["SELECTED"] = "N";
			else {
				if (in_array($el["ID"], array_column($value, "VALUE")))
					$el["SELECTED"] = "Y";
				else
					$el["SELECTED"] = "N";
			}

			return $el;
		}, $groups);

		return static::getFieldHtml($groups, ["FIELD_NAME" => $additionalParameters["VALUE"]] + $userField);
	}

	public static function getAdminListViewHTML(array $userField, ?array $additionalParameters): string	{
		static $cache = [];
		$empty_caption = '&nbsp;';
		$groups = '';
		if (!array_key_exists($additionalParameters['VALUE'], $cache)) {
			$groups = static::getList();
			if (!$groups) {
				return $empty_caption;
			}

			foreach ($groups as $group) {
				$cache[$group["ID"]] = $group["VALUE"];
			}
		}
		if (!array_key_exists($additionalParameters["VALUE"], $cache)) {
			$cache[$additionalParameters["VALUE"]] = $empty_caption;
		}
		return $cache[$additionalParameters["VALUE"]];
	}

	public static function getAdminListEditHTML(array $userField, ?array $additionalParameters): string	{
		$groups = static::getList();
		if (!$groups) {
			return '';
		}

		$groups = array_map(function ($el) use ($additionalParameters) {
			if ($el["ID"] == $additionalParameters["VALUE"]) {
				$el["SELECTED"] = "Y";
			}
			else {
				$el["SELECTED"] = "N";
			}

			return $el;
		}, $groups);

		return static::getFieldHtml($groups, ["FIELD_NAME" => $additionalParameters["VALUE"]] + $userField);
	}

	public static function getAdminListEditHTMLMulty(array $userField, ?array $additionalParameters): string {
		$groups = static::getList();
		if (!$groups) {
			return '';
		}

		$groups = array_map(function ($el) use ($additionalParameters) {
			if ($el["ID"] == $additionalParameters["VALUE"]) {
				$el["SELECTED"] = "Y";
			}
			else {
				$el["SELECTED"] = "N";
			}

			return $el;
		}, $groups);

		return static::getFieldHtml($groups, ["FIELD_NAME" => $additionalParameters["VALUE"]] + $userField);
	}

	public static function getAdminFilterHTML(array $userField, ?array $additionalParameters): string {
		$groups = static::getList();
		if (!$groups) {
			return '';
		}

		$groups = array_map(function ($el) use ($additionalParameters) {
			if ($el["ID"] == $additionalParameters["VALUE"]) {
				$el["SELECTED"] = "Y";
			}
			else {
				$el["SELECTED"] = "N";
			}

			return $el;
		}, $groups);

		return static::getFieldHtml($groups, $userField);
	}

	public static function getUIFilterProperty($arProperty, $strHTMLControlName, &$fields): void {
		$items = [];
		array_map(function ($el) use (&$items) {
			$items[$el["ID"]] = $el["VALUE"];
		}, static::getList());

		$fields["type"] = "list";
		$fields["items"] = $items;
		$fields["operators"] = [
			"default" => "=",
			"enum" => "@",
		];
		$fields["params"] = [
			"multiple" => "Y",
		];
	}
}