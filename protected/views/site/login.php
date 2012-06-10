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

$this->pageTitle=Yii::app()->name . ' - Login';




Yii::app()->clientScript->registerCss('fixMenuShadow',"
#page .container {
	position:relative;
	z-index:2;
}
",'screen',CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScript('loginFocus',"
$('#LoginForm_username').focus();
",CClientScript::POS_READY);
?>


<div id="login-box">
	<?php $form=$this->beginWidget('CActiveForm', array(
		// 'id'=>'login-form',
		'enableClientValidation'=>true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
		),
	));
	?><!--<h2><?php echo Yii::t('app','Welcome to {appName}.',array('{appName}'=>Yii::app()->name)); ?></h2>-->
<div class="form" id="login-form">
	
	<?php echo $form->label($model,'username'); ?>
	<?php echo $form->textField($model,'username'); ?>
	<?php //echo $form->error($model,'username'); ?>

	<?php echo $form->label($model,'password'); ?>
	<?php echo $form->passwordField($model,'password'); ?>
	<?php echo $form->error($model,'password'); ?>

	<?php if($model->useCaptcha && CCaptcha::checkRequirements()) { ?>
	<div class="row">
	<?php
	// CHtml::$errorCss = 'error';
	// CHtml::$errorSummaryCss = 'error';

	echo '<div>';
	$this->widget('CCaptcha',array(
		'clickableImage'=>true,
		'showRefreshButton'=>false,
		'imageOptions'=>array(
			'style'=>'display:block;cursor:pointer;',
			'title'=>Yii::t('app','Click to get a new image')
		)
	)); echo '</div>';
	echo '<p class="hint">'.Yii::t('app','Please enter the letters in the image above.').'</p>';
	echo $form->textField($model,'verifyCode');
	?>
	</div><?php } ?>
	<div class="row checkbox">
		<div class="cell">
			<?php echo CHtml::submitButton(Yii::t('app','Login'),array('class'=>'x2-button')); ?>
		</div>

		<div class="cell right" style="padding-top:2px;padding-left:5px;">
			<?php echo $form->checkBox($model,'rememberMe',array('value'=>'1','uncheckedValue'=>'0')); ?>
			<?php echo $form->label($model,'rememberMe',array('style'=>'font-size:10px;')); ?>
			<?php echo $form->error($model,'rememberMe'); ?><br>
			<?php echo CHtml::link(Yii::t('app','Login to X2Touch'),Yii::app()->getBaseUrl() . '/index.php/x2touch',array('class'=>'x2touch-link')); ?>
		</div>
	</div>
</div>
<!--<div id="login-logo"></div>-->
<?php $this->endWidget(); ?>
</div>