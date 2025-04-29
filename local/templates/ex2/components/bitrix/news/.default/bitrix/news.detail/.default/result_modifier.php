<?php B_PROLOG_INCLUDED === true || die();
//if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arResult["ID"]) {
    $order = ["DATE_ACTIVE_FROM" => "ASC", "ID" => "ASC"];
    $filter = [
        "IBLOCK_ID" => $arResult["IBLOCK_ID"],
        "ACTIVE" => "Y",
        "CHECK_PERMIOSSIONS" => "Y",
    ];

    if($arParams["CHECK_DATES"]) {
        $filter["ACTIVE_DATE"] = "Y";
    }

    $select = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL"];
    $navParams = [
        "nElementID" => $arResult["ID"],
        "nPageSize" => 1,
    ];

    $res = CIBlockElement::GetList($order, $filter, false, $navParams, $select);

    $foundCurrent = false;

    while($item = $res->GetNext()) {
        if ($item["ID"] == $arResult["ID"]) {
            $foundCurrent = true;
            continue;
        }

        $item["SHORT_NAME"] = TruncateText($item["NAME"], 50);

        if(!$foundCurrent) {
            $arResult["PREV"] = $item;
        } else {
            $arResult["NEXT"] = $item;
        }
    }
}

$this->__component->SetResultCachekeys(["ACTIVE_FROM"]);

$relateProductId = $arResult["DISPLAY_PROPERTIES"]["RELATED_PRODUCT"]["VALUE"];
if($relateProductId) {
    $relateProductFields = $arResult["DISPLAY_PROPERTIES"]["RELATED_PRODUCT"]["LINK_ELEMENT_VALUE"][$relateProductId];
    $img = CFile::ResizeImageGet(
        $relateProductFields["DETAIL_PICTURE"],
        [
            "width" => 100,
            "height" => 100,
        ],
        BX_RESIZE_IMAGE_PROPORTIONAL,
        true
    );

    $arResult["RELATED_PRODUCT"] = [
        'NAME' => $relateProductFields["NAME"],
        "DETAIL_PAGE_URL" => $relateProductFields["DETAIL_PAGE_URL"],
        "IMG" => $img,
    ];
}