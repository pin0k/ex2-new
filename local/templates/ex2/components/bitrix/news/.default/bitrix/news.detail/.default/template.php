<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="news-detail">
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img class="detail_picture" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
	<?endif?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<div class="news-date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div>
	<?endif;?>
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h3><?=$arResult["NAME"]?></h3>
	<?endif;?>
	<div class="news-detail">
	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
		<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
	<?endif;?>
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
 	<?elseif($arResult["DETAIL_TEXT"] <> ''):?>
		<?echo $arResult["DETAIL_TEXT"];?>
 	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	<div style="clear:both"></div>
	<br />
	<?foreach($arResult["FIELDS"] as $code=>$value):?>
			<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
			<br />
	<?endforeach;?>
	<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>

		<?=$arProperty["NAME"]?>:&nbsp;
		<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
			<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
		<?else:?>
			<?=$arProperty["DISPLAY_VALUE"];?>
		<?endif?>
		<br />
	<?endforeach;?>
	</div>
</div>

<div class="card-news-links" style="display: flex; justify-content: space-between;">
	<?if($arResult["PREV"]):?>
		<a href="<?=$arResult["PREV"]["DETAIL_PAGE_URL"]?>" class="news-prev btn btn-outline-primary rounded-pill btn-sm">
			<?=$arResult["PREV"]["SHORT_NAME"]?>
		</a>
	<?endif;?>
	<?if($arResult["NEXT"]):?>
		<a href="<?=$arResult["NEXT"]["DETAIL_PAGE_URL"]?>" class="news-next btn btn-outline-primary rounded-pill btn-sm">
			<?=$arResult["NEXT"]["SHORT_NAME"]?>
		</a>
	<?endif;?>
</div>


<?if($arResult["RELATED_PRODUCT"]):?>
	<?$this->SetViewTarget('news_related_product');?>
		<a class="news-related-product-block" href="<?=$arResult["RELATED_PRODUCT"]["DETAIL_PAGE_URL"];?>">
			<img class="img img_lazy lazyload object-fit-cover"
				src="<?=$arResult["RELATED_PRODUCT"]["IMG"]["src"];?>"
				alt="<?=$arResult["RELATED_PRODUCT"]["NAME"];?>"
				title="<?=$arResult["RELATED_PRODUCT"]["NAME"];?>"
				width="<?=$arResult["RELATED_PRODUCT"]["IMG"]["width"];?>"
				height="<?=$arResult["RELATED_PRODUCT"]["IMG"]["height"];?>">
			<div><?=$arResult["RELATED_PRODUCT"]["NAME"];?></div>
		</a>
	<?$this->EndViewTarget();?>
<?endif;?>
