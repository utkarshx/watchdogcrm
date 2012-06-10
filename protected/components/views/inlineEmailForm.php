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

Yii::app()->clientScript->registerScript('inlineEmailEditor',"
function setupEmailEditor() {
	if($('#email-message').data('editorSetup') != true) {
		new TINY.editor.edit('teditor',{
			id:'email-message',
			// width:560,
			height:200,
			cssclass:'tinyeditor',
			controlclass:'tecontrol',
			rowclass:'teheader',
			dividerclass:'tedivider',
			controls:['bold','italic','underline','strikethrough','|','subscript','superscript','|',
					'orderedlist','unorderedlist','|','outdent','indent','|','leftalign',
					'centeralign','rightalign','blockjustify','|','undo','redo','n',
					'font','size','unformat','|','image','hr','link','unlink','|','print'],
			footer:true,
			fonts:['Verdana','Arial','Georgia','Trebuchet MS'],
			xhtml:false,
			cssfile:'".Yii::app()->theme->getBaseUrl().'/css/tinyeditor.css'."',
			// bodyid:'editor',
			footerclass:'tefooter',
			toggle:{text:'source',activetext:'wysiwyg',cssclass:'tetoggle'},
			resize:{cssclass:'teresize'}
		});
		
		$('#email-message').data('editorSetup',true);
		
		// give send-email module focus when tinyedit clicked		
		$('#email-message-box').find('iframe').contents().find('body').click(function() {
		    if(!$('#inline-email-form').find('.wide.form').hasClass('focus-mini-module')) {
		    	$('.focus-mini-module').removeClass('focus-mini-module');
		    	$('#inline-email-form').find('.wide.form').addClass('focus-mini-module');
		    }
		});
	}
}
",CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScript('inlineEmailEditorSetup',"

if(window.hideInlineEmail)
	$('#inline-email-form').hide();
else
	setupEmailEditor();
",CClientScript::POS_READY);

?>
<div id="inline-email-form">
<?php
/* if(isset($preview) && !empty($preview)) { ?>
<div class="form">
	<?php echo $preview; ?>
</div>
<?php
} */


echo CHtml::image(Yii::app()->theme->getBaseUrl().'/images/loading.gif',Yii::t('app','Loading'),array('id'=>'email-sending-icon'));
$emailSent = false;

if(!empty($model->status)) {
	$index = array_search('200',$model->status);
	if($index !== false) {
		unset($model->status[$index]);
		$model->message = '';
		$signature = Yii::app()->params->profile->getSignature(true);
		$model->message = '<font face="Arial" size="2">'.(empty($signature)? '' : '<br><br>' . $signature).'</font>';
		$model->subject = '';
		$emailSent = true;
	}
	echo '<div class="form email-status">';
	foreach($model->status as &$status_msg) echo $status_msg." \n";
	echo '</div>';
}
?>
<div class="wide form<?php if($emailSent) echo ' hidden'; ?>">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'enableAjaxValidation'=>false,
		'method'=>'post',
	));	?>
	<div class="row">
		<?php //echo $form->error($model,'to'); ?>
		<?php echo $form->label($model,'to'); ?>
		<?php echo $form->textField($model,'to',array('id'=>'email-to','style'=>'width:400px;'));?> 
		<a href="javascript:void(0)" id="cc-toggle"<?php if(!empty($model->cc)) echo ' style="display:none;"'; ?>>[cc]</a> 
		<a href="javascript:void(0)" id="bcc-toggle"<?php if(!empty($model->bcc)) echo ' style="display:none;"'; ?>>[bcc]</a>
	</div>
	<div class="row" id="cc-row"<?php if(empty($model->cc)) echo ' style="display:none;"'; ?>>
		<?php //echo $form->error($model,'to'); ?>
		<?php echo $form->label($model,'cc'); ?>
		<?php echo $form->textField($model,'cc',array('id'=>'email-cc')); ?>
	</div>
	<div class="row" id="bcc-row"<?php if(empty($model->bcc)) echo ' style="display:none;"'; ?>>
		<?php //echo $form->error($model,'to'); ?>
		<?php echo $form->label($model,'bcc'); ?>
		<?php echo $form->textField($model,'bcc',array('id'=>'email-bcc')); ?>
	</div>
	<div class="row">
		<?php echo $form->label($model,'subject'); ?>
		<?php echo $form->textField($model,'subject'); ?>
	</div>
	<div class="row">
		<?php
		$templateList = DocChild::getEmailTemplates();
		$templateList = array('0'=>Yii::t('docs','Custom Message')) + $templateList;
		
		// $class = in_array('message',$errors)? 'error':null;
		echo $form->label($model,'message');
		echo $form->dropDownList($model,'template',$templateList,array('id'=>'email-template'));
		// echo $form->error($model,'message');
		?>
	</div>
	<div class="row" id="email-message-box"<?php //if($model->template != 0) echo ' style="display:none;"'; ?>>
		<?php echo $form->textArea($model,'message',array('id'=>'email-message','style'=>'margin:0;padding:0;')); ?>
	</div>
	<div class="row buttons">
	<?php
	echo CHtml::ajaxSubmitButton(
		Yii::t('app','Send'),
		array('inlineEmail','ajax'=>1),
		array(
			'beforeSend'=>"function(a,b) { teditor.post(); $('#email-sending-icon').show(); }",
			'replace'=>'#inline-email-form',
			'complete'=>"function(response) { $('#email-sending-icon').hide(); setupEmailEditor(); updateHistory(); }",
		),
		array(
			'id'=>'send-email-button',
			'class'=>'x2-button highlight',
			'style'=>'margin-left:-20px;',
			'name'=>'InlineEmail[submit]',
			'onclick'=>'teditor.post();',
		)
	);
	echo CHtml::ajaxSubmitButton(
		Yii::t('app','Preview'),
		array('inlineEmail','ajax'=>1,'preview'=>1),
		array(
			'beforeSend'=>"function(a,b) { teditor.post(); $('#email-sending-icon').show(); }",
			'replace'=>'#inline-email-form',
			'complete'=>"function(response) { $('#email-sending-icon').hide(); setupEmailEditor(); }",
		),
		array(
			'id'=>'preview-email-button',
			'class'=>'x2-button',
			'name'=>'InlineEmail[submit]',
			'onclick'=>'teditor.post();',
		)
	);
	echo CHtml::resetButton(Yii::t('app','Cancel'),array('class'=>'x2-button','onclick'=>"toggleEmailForm();return false;"));
	// echo CHtml::htmlButton(Yii::t('app','Send'),array('type'=>'submit','class'=>'x2-button','id'=>'send-button','style'=>'margin-left:90px;')); ?>
	</div>
	<?php $this->endWidget(); ?>
	<?php //echo CHtml::endForm(); ?>
</div>
</div>