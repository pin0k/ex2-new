<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$CONST_ID_IBLOCK_REVIEWS = 5;

$reviewsORM = CIBlockElement::GetList(
	[
		"NAME" => "ASC"
	],
	[
		"IBLOCK_ID" => $CONST_ID_IBLOCK_REVIEWS,
		"ACTIVE" => "Y"
	],
	false,
	false,
	[
		"PROPERTY_AUTHOR",
		"PROPERTY_PRODUCT"
	]
);

$arReviews = [];
while($reviews = $reviewsORM->Fetch()) {
	$arReviews[] = $reviews;
}

$arProductsWithReviews = [];
$count = 0;
$productsReviews = [];
foreach ($arResult['ITEMS'] as $key => $arItem) {
	if(in_array($arItem["ID"], array_column($arReviews, "PROPERTY_PRODUCT_VALUE"))) {
		$arItem['PRICES']['PRICE']['PRINT_VALUE'] = number_format((float)$arItem['PRICES']['PRICE']['PRINT_VALUE'], 0, '.', ' ');
		$arItem['PRICES']['PRICE']['PRINT_VALUE'] .= ' '.$arItem['PROPERTIES']['PRICECURRENCY']['VALUE_ENUM'];

		foreach ($arReviews as $value => $arReview) {
			if($arReview["PROPERTY_PRODUCT_VALUE"] == $arItem["ID"]) {
				$arItem['REVIEWS'][] = $arReview;
				$productsReviews[] = $arItem;
			}
		}

		$arResult['ITEMS'][$key] = $arItem;
		++$count;
	}
}

$arResult["COUNT_REVIEWS"] = $count;
$arResult["FIRST_REVIEW"] = reset(reset($productsReviews)["REVIEWS"])["NAME"];

$this->__component->SetResultCacheKeys(["COUNT_REVIEWS", "FIRST_REVIEW"]);
