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

// Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl().'/js/multiselect/js/jquery-1.3.2.min.js');
// Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl().'/js/multiselect/js/jquery-ui-1.7.1.custom.min.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl().'/js/multiselect/js/ui.multiselect.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl().'/js/multiselect/css/ui.multiselect.css','screen, projection');
Yii::app()->clientScript->registerCss('multiselectCss',"
.multiselect {
	width: 460px;
	height: 200px;
}
#switcher {
	margin-top: 20px;
}
",'screen, projection');


Yii::app()->clientScript->registerScript('renderMultiSelect',"
$(document).ready(function() {
	 $('.multiselect').multiselect();
});
",CClientScript::POS_HEAD);
$selected=array();
$unselected=array();
$fields=Fields::model()->findAllBySql("SELECT * FROM x2_fields ORDER BY modelName ASC");
foreach($fields as $field){
        $unselected[$field->id]=$field->modelName." - ".$field->attributeLabel;
}
$users=User::getNames();
unset($users['']);
unset($users['Anyone']);
unset($users['admin']);
/* x2temp */
$groups=Groups::model()->findAll();
foreach($groups as $group){
    $users[$group->id]=$group->name;
}
/* end x2temp */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'role-form',
	'enableAjaxValidation'=>false,
        'action'=>'roleEditor',
)); 
?>
<div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name'); ?>
        <?php echo $form->error($model,'name'); ?>
</div>
<div id="addRole">
        <?php echo $form->labelEx($model,'users'); ?>
        <?php echo $form->dropDownList($model,'users',$users,array('class'=>'multiselect','multiple'=>'multiple','size'=>7)); ?>
        <?php echo $form->error($model,'users'); ?>

    <label>View Permissions</label>
    <?php
    echo CHtml::dropDownList('viewPermissions[]',$selected,$unselected,array('class'=>'multiselect','multiple'=>'multiple', 'size'=>8));
    ?>

<br />

    <label>Edit Permissions</label>
    <?php
    echo CHtml::dropDownList('editPermissions[]',$selected,$unselected,array('class'=>'multiselect','multiple'=>'multiple', 'size'=>8));
    ?>
</div>
<br />
<div class="row buttons">
	<?php echo CHtml::submitButton(Yii::t('app','Submit'),array('class'=>'x2-button')); ?>
</div>
<?php $this->endWidget(); ?>
</div>

