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
 * Copyright � 2011-2012 by X2Engine Inc. www.X2Engine.com
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
	array('label'=>Yii::t('quotes','Quotes List'), 'url'=>array('index')),
	array('label'=>Yii::t('quotes','Create Quote'), 'url'=>array('create')),
	array('label'=>Yii::t('quotes','View Quote')),
	array('label'=>Yii::t('quotes','Update Quote'), 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>Yii::t('quotes','Add A User'), 'url'=>array('addUser', 'id'=>$model->id)),
	array('label'=>Yii::t('quotes','Add A Contact'), 'url'=>array('addContact', 'id'=>$model->id)),
	array('label'=>Yii::t('quotes','Remove A User'), 'url'=>array('removeUser', 'id'=>$model->id)),
	array('label'=>Yii::t('quotes','Remove A Contact'), 'url'=>array('removeContact', 'id'=>$model->id)),
	array('label'=>Yii::t('quotes','Delete Quote'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);
?>
<h2><?php echo Yii::t('quotes','Share Quote');?>: <b><?php echo $model->name;?></b></h2>
<?php
if(!empty($status)) {
	$index = array_search('200',$status);
	if($index !== false) {
		unset($status[$index]);
		$email = '';
		$subject = '';
	}
	echo '<div class="form">';
	foreach($status as &$status_msg) echo $status_msg." \n";
	echo '</div>';
}
// echo var_dump($errors);
?>
<div class="form">
<form method="POST" name="share-contact-form">
	<b><span<?php if(in_array('email',$errors)) echo ' class="error"'; ?>><?php echo Yii::t('contacts','E-Mail');?></span></b><br /><input type="text" name="email" size="50"<?php if(in_array('email',$errors)) echo ' class="error"'; ?> value="<?php if(!empty($email)) echo $email; ?>"><br />
	<b><span<?php if(in_array('body',$errors)) echo ' class="error"'; ?>><?php echo Yii::t('app','Message Body');?></span></b><br /><textarea name="body" style="height:200px;width:558px;"<?php if(in_array('body',$errors)) echo ' class="error"'; ?>><?php echo $body; ?></textarea><br />
	<input type="submit" class="x2-button" value="<?php echo Yii::t('app','Share');?>" />
</form>
</div>
<?php
$form = $this->beginWidget('CActiveForm', array(
	'id'=>'quotes-form',
	'enableAjaxValidation'=>false,
	'action'=>array('saveChanges','id'=>$model->id),
));
?>
<h2><?php echo Yii::t('quotes','Quote:'); ?> <b><?php echo $model->name; ?></b></h2>
<?php
$this->renderPartial('_detailView',array('model'=>$model,'form'=>$form,'currentWorkflow'=>$currentWorkflow)); 
$this->endWidget(); ?>