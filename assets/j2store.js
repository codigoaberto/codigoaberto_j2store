// JavaScript Document
jQuery(document).ready(function() {
	jQuery('#cck_tabs1Tabs').append('<li><a href="#tab_j2store" data-toggle="tab">J2store options</a></li>');
 	jQuery('#cck_tabs1Content').append('<div id="tab_j2store" class="form-horizontal tab-pane">'+jQuery('#j2store_info').html()+'</div>');
	jQuery('#j2store_info').html('');
	jQuery('#cck1r_j2store').html('');
});