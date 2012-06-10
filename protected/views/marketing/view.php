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
	array('label'=>Yii::t('marketing','Marketing Action List'), 'url'=>array('index')),
	array('label'=>Yii::t('marketing','Create Marketing Action'), 'url'=>array('create')),
	array('label'=>Yii::t('marketing','View Marketing Action')),
	array('label'=>Yii::t('marketing','Update Marketing Action'), 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>Yii::t('marketing','Update Result'), 'url'=>array('setResult','id'=>$model->id)),
);
?>
<h1><?php echo Yii::t('marketing','Marketing Action: {name}',array('{name}'=>$model->name)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>Yii::app()->request->baseUrl.'/themes/'.Yii::app()->theme->name.'/css/detailview',
	'attributes'=>array(
		'name',
		'cost',
		'result',
		array(
			'name'=>'createDate',
			'type'=>'raw',
			'value'=>date("Y-m-d H:i:s",$model->createDate),
		),
		'description',
	),
)); ?>

<h2>Notes</h2>

<form name="noteForm" action="addNote" method="POST">
	<textarea name="note" rows="4" cols="69" onfocus="clearText(this);">Add a note...</textarea><br /><br />
	<input type="hidden" name="type" value="marketing" />
	<input type="hidden" name="associationId" value="<?php echo $model->id ?>" />
	<input type="submit" value="Add Note!" />
</form>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProviderNotes,
	'itemView'=>'../notes/_view',
)); ?>


<script>

/*
Clear default form value script- By Ada Shimar (ada@chalktv.com)
*/

function clearText(thefield){
if (thefield.defaultValue==thefield.value)
thefield.value = "";
} 
</script>
