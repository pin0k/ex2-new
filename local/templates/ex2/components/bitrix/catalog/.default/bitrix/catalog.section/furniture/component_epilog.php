<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult["COUNT_REVIEWS"]) && $arResult["COUNT_REVIEWS"] > 0):
    $APPLICATION->SetPageProperty("ex2_meta", str_replace("#count#", $arResult["COUNT_REVIEWS"], $APPLICATION->GetProperty('ex2_meta')));
endif;    

if(!empty($arResult["FIRST_REVIEW"])):
    $APPLICATION->AddViewContent("REVIEWS_ADDITIONAL", GetMessage("REVIEWS_ADDITIONAL", [
        "#STRING#" => $arResult["FIRST_REVIEW"],
    ]));
endif;