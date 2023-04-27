// JavaScript Document

jQuery(document).ready(function($){

	$('#loginform label[for=user_login]').empty().html('<input id="user_loginform" class="input" type="text" size="20" value="" name="log" placeholder="Username">');

	$('#loginform label[for=user_pass]').empty().html('<input id="user_pass" class="input" type="password" size="20" value="" name="pwd" placeholder="Password">');

	$('#loginform label[for=rememberme]').empty().html('<input id="rememberme" type="checkbox" value="forever" name="rememberme"> Stay Logged In');

	$('<a class="forgot" title="Password Lost and Found" href="/login/?action=lostpassword">Forgot?</a>').appendTo('#loginform label[for=user_pass]');

	$('#loginform #nav, .newsociallogins, #loginform h3').remove();

});