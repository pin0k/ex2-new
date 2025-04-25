<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="accordion">
	<?foreach ($arResult['ITEMS'] as $key=>$val):?>
		<?
			$this->AddEditAction($val['ID'],$val['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($val['ID'],$val['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('FAQ_DELETE_CONFIRM', array("#ELEMENT#" => CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_NAME")))));
		?> 
    <div class="accordion-item" id="<?=$this->GetEditAreaId($val['ID']);?>">
        <h2 class="accordion-header">
            <button class="accordion-button <?=($key != 0 ? 'collapsed' : '')?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?=$val["ID"]?>" aria-expanded="<?=($key != 0 ? 'false' : 'true')?>" aria-controls="<?=$val["ID"]?>">
				<?=$val['NAME']?>
            </button>
        </h2>
        <div id="<?=$val["ID"]?>" class="accordion-collapse collapse <?=($key != 0 ? '' : 'show')?>">
            <div class="accordion-body">
                <div><?=$val['PREVIEW_TEXT']?></div>
                <div><?=$val['DETAIL_TEXT']?></div>
            </div>
        </div>
    </div>
	<?endforeach;?>
</div>
