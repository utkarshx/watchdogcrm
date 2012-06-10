<?php
/*********************************************************************************
 * The X2CRM by X2Engine Inc. is free software. It is released under the terms of 
 * the following BSD License.
 * http://www.opensource.org/licenses/BSD-3-Clause
 * 
 * X2Engine Inc.
 * P.O. Box 66752
 * Scotts Valley, California 95066 USA
 * 
 * Company website: http://www.x2engine.com 
 * Community and support website: http://www.x2community.com 
 * 
 * Copyright © 2011-2012 by X2Engine Inc. www.X2Engine.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * - Redistributions of source code must retain the above copyright notice, this 
 *   list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this 
 *   list of conditions and the following disclaimer in the documentation and/or 
 *   other materials provided with the distribution.
 * - Neither the name of X2Engine or X2CRM nor the names of its contributors may be 
 *   used to endorse or promote products derived from this software without 
 *   specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 ********************************************************************************/

$this->menu=array(
		array('label'=>Yii::t('actions','Today\'s Actions'),'url'=>array('index')),
		array('label'=>Yii::t('actions','All My Actions'),'url'=>array('viewAll')),
		array('label'=>Yii::t('actions','Everyone\'s Actions'),'url'=>array('viewGroup')),
		// array('label'=>Yii::t('actions','Create Lead'),'url'=>array('quickCreate')),
		array('label'=>Yii::t('actions','Create'),'url'=>array('create')), 
	);

$profile = ProfileChild::model()->findByPk(Yii::app()->user->id);
$this->showActions = $profile->showActions;
if(!$this->showActions) // if user hasn't saved a type of action to show, show uncomple actions by default
    $this->showActions = 'uncomplete';

if($this->showActions == 'uncomplete')
	$model->complete = 'No';
else if ($this->showActions == 'complete')
	$model->complete = 'Yes';
else
	$model->complete = '';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('actions-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

Yii::app()->clientScript->registerScript('completeUncompleteSelected', "
function completeSelected() {
	var checked = $.fn.yiiGridView.getChecked('actions-grid', 'C_gvCheckbox');
	$.post('completeSelected', {'Actions': checked}, function() {jQuery.fn.yiiGridView.update('actions-grid')});
}
function uncompleteSelected() {
	var checked = $.fn.yiiGridView.getChecked('actions-grid', 'C_gvCheckbox');
	$.post('uncompleteSelected', {'Actions': checked}, function() {jQuery.fn.yiiGridView.update('actions-grid')});
}

function toggleShowActions() {
	var show = $('#dropdown-show-actions').val(); // value of dropdown (which actions to show)
	$.post('saveShowActions', {ShowActions: show}, function() {
		$.fn.yiiGridView.update('actions-grid', {data: $.param($('#actions-grid input[name=\"Actions[complete]\"]'))});
	});
}
",CClientScript::POS_HEAD);

function trimText($text) {
	if(strlen($text)>150)
		return substr($text,0,147).'...';
	else
		return $text;
}

?>

<h2><?php echo Yii::t('actions','Manage Actions'); ?></h2>
<?php echo Yii::t('app','You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.'); ?>
<br />
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php /*
$form=$this->beginWidget('CActiveForm', array(
	'action'=>array('updateSelected'),
    'enableAjaxValidation'=>false,
)); */
$this->widget('application.components.X2GridView', array(
	'id'=>'actions-grid',
	'baseScriptUrl'=>Yii::app()->request->baseUrl.'/themes/'.Yii::app()->theme->name.'/css/gridview',
	'template'=> '<div class="title-bar">'
		.CHtml::link(Yii::t('app','Advanced Search'),'#',array('class'=>'search-button')) . ' | '
		.CHtml::link(Yii::t('app','Clear Filters'),array(Yii::app()->controller->action->id,'clearFilters'=>1)) . ' | '
		.CHtml::link(Yii::t('app','Columns'),'javascript:void(0);',array('class'=>'column-selector-link'))
		.'{summary}</div>{items}{pager}',
	'dataProvider'=>$model->searchAdmin(),
	// 'enableSorting'=>false,
	// 'model'=>$model,
	'filter'=>$model,
	// 'columns'=>$columns,
	'modelName'=>'Actions',
	'viewName'=>'actionsadmin',
	// 'columnSelectorId'=>'contacts-column-selector',
	'defaultGvSettings'=>array(
		'gvCheckbox'=>28,
		'actionDescription'=>220,
		// 'associationType'=>93,
		'dueDate'=>93,
//		'complete'=>89,
		'assignedTo'=>112,
		'gvControls'=>72,

	),
	'specialColumns'=>array(
		'actionDescription'=>array(
			'name'=>'actionDescription',
			'value'=>'CHtml::link(($data->type=="attachment")? MediaChild::attachmentActionText($data->actionDescription) : CHtml::encode(trimText($data->actionDescription)),array("view","id"=>$data->id))',
			'type'=>'raw',
		),
	),
	'enableControls'=>true,
));
?>
<br />
<div class="row buttons">
	<a class="x2-button" href="#" onClick="completeSelected();"><?php echo Yii::t('actions','Complete Selected'); ?></a>
	<a class="x2-button" href="#" onClick="uncompleteSelected()"><?php echo Yii::t('actions','Uncomplete Selected'); ?></a>
</div>
<?php //$this->endWidget(); ?>


