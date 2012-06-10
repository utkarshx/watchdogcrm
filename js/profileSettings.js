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

function highlightSave() {
	$('#save-changes').addClass('highlight'); //css('background','yellow');
}
$(document).ready(function() {
	$('#backgroundColor').modcoder_excolor({
		hue_bar : 3,
		hue_slider : 5,
		border_color : '#aaa',
		sb_border_color : '#d6d6d6',
		round_corners : false,
		shadow_color : '#000000',
		background_color : '#f0f0f0',
		backlight : false,
		callback_on_ok : function() {
			$('#backgroundColor').change();
			// var text = $('#backgroundColor').val();
			// $('#backgroundColor').val(text.substring(1,7));
			// $('body, #header').css('background-color',text);
			// highlightSave();
		}
	});
/* 	$('#menuBgColor').modcoder_excolor({
		hue_bar : 3,
		hue_slider : 5,
		border_color : '#aaa',
		sb_border_color : '#d6d6d6',
		round_corners : false,
		shadow_color : '#000000',
		background_color : '#f0f0f0',
		backlight : false,
		callback_on_ok : function() {
			var text = $('#menuBgColor').val();
			$('#menuBgColor').val(text.substring(1,7));
			$('#header').css('background-color',text);
			highlightSave();
		}
	}); */
	$('#menuTextColor').modcoder_excolor({
		hue_bar : 3,
		hue_slider : 5,
		border_color : '#aaa',
		sb_border_color : '#d6d6d6',
		round_corners : false,
		shadow_color : '#000000',
		background_color : '#f0f0f0',
		backlight : false,
		callback_on_ok : function() {
			$('#menuTextColor').change();
			// var text = $('#menuTextColor').val();
			// $('#menuTextColor').val(text.substring(1,7));
			// $('#main-menu-bar a, #main-menu-bar span').css('color',text);
			// highlightSave();
		}
	});
	
	$('#backgroundColor').change(function() {
		var text = $('#backgroundColor').val();
		if(text == '')
			$('#header').css('background-color','#000');
		else {
			$('#backgroundColor').val(text.substring(1,7));
			$('#header').css('background-color',text);
		}
		highlightSave();
		
	});
	$('#menuTextColor').change(function() {
		var text = $('#menuTextColor').val();
		if(text == '')
			$('#main-menu-bar a, #main-menu-bar span').css('color','#fff');
		else {
			$('#menuTextColor').val(text.substring(1,7));
			$('#main-menu-bar a, #main-menu-bar span').css('color',text);
		}
		highlightSave();
	});
	
	
	
	$('#ProfileChild_enableFullWidth').change(function() {
		window.enableFullWidth = $(this).is(':checked');
		$(window).resize();
		highlightSave();
	});
	
	
	
});



// background uploader
function showAttach() {
	e=document.getElementById('attachments');
	if(e.style.display=='none')
		e.style.display='block';
	else
		e.style.display='none';
}
var ar_ext = ['png', 'jpg','jpe','jpeg','gif','svg'];        // array with allowed extensions

function checkName() {
// - www.coursesweb.net
	// get the file name and split it to separe the extension
	var name = $('#backgroundImg').val();
	var ar_name = name.split('.');

	// check the file extension
	var re = 0;
	for(var i=0; i<ar_ext.length; i++) {
		if(ar_ext[i] == ar_name[1].toLowerCase()) {
			re = 1;
			break;
		}
	}
	// if re is 1, the extension is in the allowed list
	if(re==1) {
		// enable submit
		$('#upload-button').removeAttr('disabled');
	} else {
		// delete the file name, disable Submit, Alert message
		$('#backgroundImg').val('');
		$('#upload-button').attr('disabled','disabled');
		alert('\".'+ ar_name[1]+ '\" is not an file type allowed for upload');
	}
}