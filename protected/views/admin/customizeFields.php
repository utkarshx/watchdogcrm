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
?><h3><?php echo Yii::t('admin','Customize Fields'); ?></h3>
<?php echo Yii::t('admin','This form will allow you to rename or show/hide any field on the four major models (Contacts, Actions, Sales and Accounts).'); ?><br><br>
<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'criteria-form',
		'enableAjaxValidation'=>false,
		'action'=>'customizeFields',
	)); ?>
	<em><?php echo Yii::t('app','Fields with <span class="required">*</span> are required.'); ?></em><br>
	<div class="row">
		<?php echo $form->labelEx($model,'modelName'); ?>
		<?php 
                $modules=Modules::model()->findAll();
                foreach($modules as $module){
                    if($module->editable){
                        $arr[$module->name]=$module->title;
                    }
                }
                echo $form->dropDownList($model,'modelName',$arr,
			array(
			'empty'=>'Select a model',
			'ajax' => array(
			'type'=>'POST', //request type
			'url'=>CController::createUrl('admin/getAttributes'), //url to call.
			//Style: CController::createUrl('currentController/methodToCall')
			'update'=>'#dynamicFields', //selector to update
			//'data'=>'js:"modelType="+$("'.CHtml::activeId($model,'modelType').'").val()' 
			//leave out the data key to pass all form values through
			))); ?>
		<?php echo $form->error($model,'modelName'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'fieldName'); ?>
		<?php echo $form->dropDownList($model,'fieldName',array(),array('empty'=>'Select a model first','id'=>'dynamicFields',
                    'ajax' => array(
			'type'=>'POST', //request type
			'url'=>CController::createUrl('admin/getFieldData'), //url to call.
			//Style: CController::createUrl('currentController/methodToCall')
			'success'=>'updateFields', //selector to update
			//'data'=>'js:"modelType="+$("'.CHtml::activeId($model,'modelType').'").val()' 
			//leave out the data key to pass all form values through
			))); ?>
		<?php echo $form->error($model,'fieldName'); ?>
	</div>
	<br>
	<div class="row">
		<div>
		Please enter the new name for your chosen field.<br>
		Leave blank if you don't want to change it.</div><br>
		<?php echo $form->labelEx($model,'attributeLabel'); ?>
		<?php echo $form->textField($model,'attributeLabel', array('id'=>'attributeLabel')); ?>
		<?php echo $form->error($model,'attributeLabel'); ?>
	</div>
	
	<div class="row">
            <?php echo $form->labelEx($model,'type'); ?>
            <?php echo $form->dropDownList($model,'type',
                    array(
                        'varchar'=>'Single Line Text',
                        'text'=>'Multiple Line Text Area',
                        'date'=>'Date',
                        'dropdown'=>'Dropdown',
                        'int'=>'Number',
                        'email'=>'E-Mail',
                        'currency'=>'Currency',
                        'url'=>'URL',
                        'float'=>'Decimal',
                        'boolean'=>'Checkbox',
                        'link'=>'Lookup',
                        'rating'=>'Rating',
                        'assignment'=>'Assignment'
                    ),
                array(
                'id'=>'fieldType',
                'ajax' => array(
                'type'=>'POST', //request type
                'url'=>CController::createUrl('admin/getFieldType'), //url to call.
                //Style: CController::createUrl('currentController/methodToCall')
                'update'=>'#edit_dropdown', //selector to update
                //'data'=>'js:"modelType="+$("'.CHtml::activeId($model,'modelType').'").val()' 
                //leave out the data key to pass all form values through
                ))); ?>
            <?php echo $form->error($model,'type'); ?> 
        </div>
    
        <div class="row" id="edit_dropdown">

        </div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'required');?>
		<?php echo $form->checkBox($model,'required',array('id'=>'required'));?>
		<?php echo $form->error($model,'required');?>
	</div>
        
        <div class="row">
            <?php echo $form->labelEx($model,'searchable');?>
            <?php echo $form->checkBox($model,'searchable',array('id'=>'searchable','onclick'=>'$("#relevance_box").toggle();'));?>
            <?php echo $form->error($model,'searchable');?>
        </div>
        
        <div class="row" id ="relevance_box" style="display:none">
            <?php echo $form->labelEx($model,'relevance'); ?>
            <?php echo $form->dropDownList($model,'relevance',array('Low'=>'Low',"Medium"=>"Medium","High"=>"High"),array("id"=>"relevance",'options'=>array('Medium'=>array('selected'=>true)))); ?>
            <?php echo $form->error($model,'relevance'); ?> 
        </div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('app','Save'):Yii::t('app','Save'),array('class'=>'x2-button')); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
<script>
    function updateFields(data){
        data=$.parseJSON(data);
        $('#attributeLabel').val(data.attributeLabel);
        $('#fieldType').val(data.type);
        $('#edit_dropdown').html(data.dropdown);
        if(data.required==1){
            $('#required').attr("checked",true);
        }else{
            $('#required').attr("checked",false);
        }
        if(data.searchable==1){
            $('#relevance_box').show();
            $('#searchable').attr("checked",true);
        }else{
            $('#relevance_box').hide();
            $('#searchable').attr("checked",false);
        }
        $('#relevance').val(data.relevance)
    }
</script>