<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
jimport('joomla.utilities.date');

require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/library/prices.php');
require_once (JPATH_SITE.'/components/com_j2store/helpers/cart.php');
require_once (JPATH_SITE.'/components/com_j2store/helpers/downloads.php');
jimport('joomla.filesystem.file');
if (version_compare(JVERSION, '3.0', 'lt')) {
	if(JFile::exists(JPATH_LIBRARIES.'/joomla/database/table/content.php')) {
		require_once(JPATH_LIBRARIES.'/joomla/database/table/content.php');
	}
}
class plgCCK_FieldJ2store extends JCckPluginField
{
	protected static $type		=	'j2store';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$value	= "{j2storecart ".$config['pk']."}";
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		$value		=	htmlspecialchars( $value );
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		if($field->storage_location) {
		  $godata = array();
		  	$app = JFactory::getApplication();
         $articleId = $app->input->get('id') ? $app->input->get('id') : 0;
        if($articleId) {
			$db = JFactory::getDbo();
			$query='SELECT * FROM #__j2store_prices' .
					' WHERE article_id = '.(int) $articleId;
			$db->setQuery($query);
			$price = $db->loadObject();
			if ($db->getErrorNum())
			{
				$this->_subject->setError($db->getErrorMsg());
				return false;
			}
			if( isset($price) )
			{
				$godata['product_enabled']=$price->product_enabled;
				$godata['item_price']=$price->item_price;
				$godata['special_price']=$price->special_price;
				$godata['item_tax']=$price->item_tax;
				$godata['item_shipping']=$price->item_shipping;
				$godata['item_sku']=$price->item_sku;
			//	$data->attribs['item_qty'] = $stock;
			}
	}
		$lang = JFactory::getLanguage();
$lang->load('plg_cck_field_j2store');
$lang->load('com_j2store');
		$name_form = 'com_content.article';
		
		$doc = JFactory::getDocument();
       $doc->addScript(self::$path.'assets/j2store.js');
		// add fields
		$app = JFactory::getApplication();
	 	$product_id = $app->input->get('id');
	 	$html = '';
	 	$html .="<div class='control-group' style='display:none'><div class='control-label'><label>".JText::_('Product ID')."</label></div><div class='controls'><table class='adminlist table table-striped'>";
	 	if(isset($product_id)){
	 		$html .= '<tr><td>';
	 		$html .= '<label class="j2store_product_id">';
	 		$html .= $product_id;
	 		$html .= '</label>';
	 		$html .= '</td></tr><tr><td>';

	 		$html .= JText::_('PLG_J2STORE_PRODUCT_SHORT_TAG').": {j2storecart $product_id}";
	 		$html .= '&nbsp;&nbsp;';
	 		$html .= JHtml::tooltip(JText::_('PLG_J2STORE_PRODUCT_SHORT_TAG_HELP'), JText::_('PLG_J2STORE_PRODUCT_SHORT_TAG'),'tooltip.png', '', '', false);
	 		$html .= '</td></tr>';
	 	} else {
	 		$html .= '<div class="alert alert-info">';
	 		$html .= JText::_('PLG_J2STORE_PRODUCT_ID_DESC');
	 		$html .= '</div>';
	 	}
	 	$html .= '</table></div></div>';//Set this value from DB, etc. 
		$currentValue ='0';
$arr = array(
  JHTML::_('select.option', '1', JText::_('YES') ),
  JHTML::_('select.option', '0', JText::_('NO') )
);
 
$html .= "<div class='control-group'><div class='control-label'><label>".JText::_('PLG_J2STORE_ENABLE_CART_LABEL')."</label></div>".JHTML::_('select.radiolist', $arr, 'product_enabled', 'class="btn"', 'value', 'text', $godata['product_enabled']).'</div>';
$html .= '<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_ITEM_SKU_LABEL').'</label></div><div class="controls"><input name="item_sku"
				type="text"
				id="item_sku"
				description="'.JText::_('PLG_J2STORE_ITEM_SKU_DESC').'"
				label="'.JText::_('PLG_J2STORE_ITEM_SKU_LABEL').'"
				message="'.JText::_('PLG_J2STORE_ITEM_SKU_MESSAGE').'"
				value ="'.$godata['item_sku'].'"
				size="30"
			/></div></div>';
$html .= '<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_ITEM_PRICE_LABEL').'</label></div><div class="controls"><input name="item_price"
				type="text"
				id="item_price"
				description="'.JText::_('PLG_J2STORE_ITEM_PRICE_DESC').'"
				label="'.JText::_('PLG_J2STORE_ITEM_PRICE_LABEL').'"
				message="'.JText::_('PLG_J2STORE_ITEM_PRICE_MESSAGE').'"
				size="30"
				value="'.$godata['item_price'].'"
			/></div></div>';
$html .= '<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_SPECIAL_PRICE_LABEL').'</label></div><div class="controls"><input name="special_price"
				type="text"
				id="special_price"
				description="'.JText::_('PLG_J2STORE_SPECIAL_PRICE_DESC').'"	
				label="'.JText::_('PLG_J2STORE_SPECIAL_PRICE_LABEL').'"
				message="'.JText::_('PLG_J2STORE_SPECIAL_PRICE_DESC').'"
				value ="'.$godata['special_price'].'"
				size="30"
			/></div></div>';
		$tax_default = 0;
		$db = JFactory::getDBO();
		$option ='';
		$query = 'SELECT taxprofile_id AS value, taxprofile_name AS text FROM #__j2store_taxprofiles WHERE state=1 ORDER BY taxprofile_id';
		$db->setQuery( $query );
		$taxprofiles = $db->loadObjectList();
		$types[] 		= JHTML::_('select.option',  '0', JText::_( 'J2STORE_SELECT_TAXPROFILE' ) );
		foreach( $taxprofiles as $item )
		{
			$types[] = JHTML::_('select.option',  $item->value, JText::_( $item->text ) );
		}

		$lists 	= '<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_ITEM_TAX_LABEL').'</label></div><div class="controls">'.JHTML::_('select.genericlist',   $types, 'item_tax', 'id ="item_tax" class="inputbox list" size="1" '.$option.'', 'value', 'text', $godata['item_tax']).'</div></div>';
$html .= $lists;
$arrs = array(
  JHTML::_('select.option', '1', JText::_('YES') ),
  JHTML::_('select.option', '0', JText::_('NO') )
);
 
$html .= '<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_ITEM_ENABLE_SHIPPING_LABEL').'</label></div>'.JHTML::_('select.radiolist', $arrs, 'item_shipping', null, 'value', 'text', $godata['item_shipping']).'</div>';

$html .='<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_ITEM_METRICS_LABEL').'</label></div><div class="controls"><table id="attribute_options_table" class="adminlist table table-striped table-bordered j2store_metrics"><tr><td>';
$app = JFactory::getApplication();
		$fieldName = 'item_metrics';
 		$cid = $app->input->get('id', 0);
		if($cid) {
 			$row = $this->getData($cid);
			//dimentions
           $html .='<label>'.JText::_('J2STORE_METRICS_DIMENTIONS').'</label>';
 			$html .="<input class='' name='jform[attribs][item_metrics][item_length]' value='{$row->item_length}' />";
 			$html .="<input class='' name='jform[attribs][item_metrics][item_width]' value='{$row->item_width}' />";
 			$html .="<input class='' name='jform[attribs][item_metrics][item_height]' value='{$row->item_height}' />";
 			//length class
 			$html .='</tr><tr><td>';
 			$html .='<label>'.JText::_('J2STORE_METRICS_LENGTH_CLASS').'</label>';
 			$html .= $this->getLengthClass($cid);
 			$html .='</tr><tr><td>';

 			//weight
 			$html .='<label>'.JText::_('J2STORE_METRICS_WEIGHT').'</label>';
 			$html .="<input class='' name='jform[attribs][item_metrics][item_weight]' value='{$row->item_weight}' />";
 			$html .='</tr><tr><td>';

 			//weight class
 			$html .='<label>'.JText::_('J2STORE_METRICS_WEIGHT_CLASS').'</label>';
 			$html .= $this->getWeightClass($cid);

		} else {
			$html .= JText::_('J2STORE_METRICS_SAVE_TO_ADD');
		}

		$html .= '</td></tr></table></div></div>';
require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/strapper.php');
$html .='<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_ITEM_OPTION_LABEL').'</label></div><div class="controls">';
	J2StoreStrapper::addJS();
		J2StoreStrapper::addCSS();
		$fieldName = 'item_options';

 		$doc = JFactory::getDocument();
 		$app = JFactory::getApplication();
 		$yes = JText::_('J2STORE_YES');
 		$no = JText::_('J2STORE_NO');
 		$script = "
 		if(typeof(j2store) == 'undefined') {
			var j2store = {};
		}
		if(typeof(j2store.jQuery) == 'undefined') {
			j2store.jQuery = jQuery.noConflict();
		}

 		(function($) {
 		$(document).ready(function() {
 			$('#optionselector').autocomplete({
 				source : function(request, response) {
 					$.ajax({
 						type : 'post',
 						url :  'index.php?option=com_j2store&view=options&task=getOptions',
 						data : 'q=' + request.term,
 						dataType : 'json',
 						success : function(data) {
 							$('#optionselector').removeClass('optionsLoading');
 							response($.map(data, function(item) {
 								return {
 									label: item.option_name+' ('+item.option_unique_name+')',
 									value: item.option_id
 								}
 							}));
 						}
 					});
 				},
 				minLength : 2,
 				select : function(event, ui) {
 					$('<tr><td class=\"addedOption\">' + ui.item.label+ '</td><td><select name=\"jform[attribs][item_options][product_option_required]['+ ui.item.value+']\" ><option value=\"0\">$no</option><option value=\"1\">$yes</option></select></td><td><span class=\"optionRemove\" onclick=\"j2store.jQuery(this).parent().parent().remove();\">x</span><input type=\"hidden\" value=\"' + ui.item.value+ '\" name=\"jform[attribs][item_options][product_option_ids][]\" /></td></tr>').insertBefore('.a_options');
 					this.value = '';
 					return false;
 				},
 				search : function(event, ui) {
 					$('#optionselector').addClass('optionsLoading');
 				}
 			});

 		});
 		})(j2store.jQuery);
 		";
 		$doc->addScriptDeclaration($script);
 		$product_id = $app->input->get('id');

				//$lists = $this->_getSelectProfiles($this->name, $this->id,$this->value);
				$html .='<table id="attribute_options_table" class="adminlist table table-striped table-bordered j2store">';
				$html .='<thead>';
				$html .='<th>'.JText::_('J2STORE_OPTION_NAME').'</th>';
				$html .='<th>'.JText::_('J2STORE_OPTION_REQUIRED').'</th>';
				$html .='<th>'.JText::_('J2STORE_OPTION_REMOVE').'</th>';
				$html .='</thead>';
				if($product_id ) {
					$html .= $this->_getCurrentOptions($product_id);
				}
				$html .='<tbody>';
				$html .='<tr class="a_options"><td colspan="3">';
				$html .='<label class="attribute_option_label">';
				$html .=JText::_('J2STORE_OPTIONFIELD_ADD_OPTIONS');
				$html .='</label>';
				$html .='<input id="optionselector" type="text" value="" />';
				$html .='</td></tr>';
				$html.='<tr><td colspan="3">';
				$html .='<div class="alert alert-block alert-info">';
				$html .=JText::_('J2STORE_OPTIONFIELD_ADD_OPTIONS_HELP_TEXT');
				$html .='</div>';
				$html .='</td></tr>';
				$html .='<tr><td colspan="3">';
				$html .=J2StorePopup::popup("index.php?option=com_j2store&view=products&task=setpaimport&tmpl=component&product_id={$product_id}", JText::_('J2STORE_IMPORT_PRODUCT_OPTIONS'), array(0));
				$html .='</a>';
				$html .='</td></tr>';
				$html .='</tbody></table>';
$html .='</div></div>';
$fieldName = 'item_attachment';
$html .='<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_ITEM_ATTACHMENT_LABEL').'</label></div><div class="controls">';
		$html .='<table  class="adminlist table j2store_itemfiles"><tr><td>';
		$cid = JRequest::getVar('id');
		if($cid) {
			$link = 'index.php?option=com_j2store&view=products&task=setfiles&id='.$cid.'&tmpl=component';
			$files = $this->getProductFiles($cid);
			if(!empty($files)) {
				$html .=$files;
			}
		$html .= J2StorePopup::popup( $link, JText::_( "PLG_J2STORE_ADD_REMOVE_FILES" ) );
		$html .= JText::_('PLG_J2STORE_FILES_NOTE');	
		} else {
			$html .= JText::_('PLG_J2STORE_CLICK_TO_UPLOAD_FILES');
		}
		$html .= '</td></tr></table>';	
		$html .= '</div></div>';
			
$html .='<div class="control-group"><div class="control-label"><label>'.JText::_('PLG_J2STORE_ITEM_CARTEXT_LABEL').'</label></div><div class="controls">';	
		$html .='<input name="item_cart_text"
				type="text"
				id="item_cart_text"
				description="'.JText::_('PLG_J2STORE_ITEM_CARTEXT_DESC').'"
				label="'.JText::_('PLG_J2STORE_ITEM_CARTEXT_LABEL').'"
				message="'.JText::_('PLG_J2STORE_ITEM_CARTEXT_MESSAGE').'"
				size="30"
			/></div></div>';
	$form =  '<div id="j2store_info">'.$html.'</div>';

		}
		
		
		
		
		// $form	=	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
		$field->value	=	$value;
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
protected function getData($product_id) {

	 	$row = JTable::getInstance('Prices', 'Table');
	 	if($product_id) {
	 		$row->load(array('article_id'=>$product_id));
	 	}
	 	return $row;
	 }
protected function getLengthClass($product_id) {
	 	$product = $this->getData($product_id);

	 	require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/models/lengths.php');
		$model = new J2StoreModelLengths;
		$lengths = $model->getLengths();
		//generate country filter list
		$length_options = array();
			$length_options[] = JHTML::_('select.option', 0, JText::_('J2STORE_METRICS_SELECT_LENGTH_CLASS'));
		foreach($lengths as $row) {
			$length_options[] =  JHTML::_('select.option', $row->length_class_id, $row->length_title);
		}

		return JHTML::_('select.genericlist', $length_options, 'jform[attribs][item_metrics][item_length_class_id]', 'onchange=', 'value', 'text', $product->item_length_class_id);
	 }

	 protected function getWeightClass($product_id) {
	 	$product = $this->getData($product_id);
	 	require_once(JPATH_ADMINISTRATOR.'/components/com_j2store/models/weights.php');
	 	$model = new J2StoreModelWeights;
	 	$weights = $model->getWeights();
	 	//generate country filter list
	 	$weight_options = array();
	 	$weight_options[] = JHTML::_('select.option', 0, JText::_('J2STORE_METRICS_SELECT_WEIGHT_CLASS'));
	 	foreach($weights as $row) {
	 		$weight_options[] =  JHTML::_('select.option', $row->weight_class_id, $row->weight_title);
	 	}

	 	return JHTML::_('select.genericlist', $weight_options, 'jform[attribs][item_metrics][item_weight_class_id]', 'onchange=', 'value', 'text', $product->item_weight_class_id);

	 }
	protected function _getCurrentOptions($product_id) {

		$html = '';
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('po.product_option_id, po.option_id, po.required, o.option_unique_name, o.option_name, o.type');
		$query->from('#__j2store_product_options AS po');
		$query->join('LEFT', '#__j2store_options AS o ON po.option_id = o.option_id');
		$query->where('po.product_id='.$product_id);
		$query->order('po.product_option_id');
		$db->setQuery( $query );
		$pa_options = $db->loadObjectList();
		if(count($pa_options)) {

			foreach($pa_options as $pa_option) {
				$html .='<tr id="pao_current_option_'.$pa_option->product_option_id.'">';
				$html .='<td>';
				$html .='<strong>'.$pa_option->option_name.'</strong>';
				$html .='&nbsp;&nbsp;<small>('.$pa_option->option_unique_name.')</small>';
				$html .= '&nbsp;&nbsp;<br />';
				$html .= '<small>'.JText::_('J2STORE_OPTION_TYPE').':&nbsp;'.JText::_('J2STORE_'.JString::strtoupper($pa_option->type)).'</small>';

				if($pa_option->type == 'select' || $pa_option->type == 'radio' || $pa_option->type == 'checkbox') {
					$html .= '&nbsp;&nbsp;<br />';
					$html .= J2StorePopup::popup( "index.php?option=com_j2store&view=products&task=setproductoptionvalues&product_option_id=".$pa_option->product_option_id."&tmpl=component", JText::_( "J2STORE_OPTION_SET_VALUES" ), array());
				}
				$html .='</td>';
				$html .='<td>';
				$html .= $this->_getOptionRequired($pa_option->product_option_id, $pa_option->required);
				$html .='</td>';
				$html .='<td>';
				$html .='<span class="optionRemove" onClick="removePAOption('.$pa_option->product_option_id.')">X</span>';
				$html .='</td>';
			}

		}


		return $html;

	}

	function _getOptionRequired($product_option_id, $required=0) {

		$html = "";
$html .= "<fieldset id='product_option_required' class='radio'>";
		$html .="<label class=''>";
		$html .="<input type='radio' class='radio' name='jform[attribs][item_options][product_option_required_save][{$product_option_id}]' value='1'";
		if($required == 1) $html .="checked='checked'";
		$html .="/>";
		$html .=JText::_('J2STORE_YES')."</label>";
		$html .="<label class=''>";
		$html .="<input type='radio' class='radio' name='jform[attribs][item_options][product_option_required_save][{$product_option_id}]' value='0'";
		if($required == 0) $html .="checked='checked'";
		$html .="/>";
		$html .=JText::_('J2STORE_NO')."</label>";
		$html .= "</fieldset>";
		return $html;
	}
	function getProductFiles($product_id) {

		$db = JFactory::getDBO();
		$query = 'SELECT a.* FROM #__j2store_productfiles AS a WHERE a.product_id='. (int) $product_id
				 .' ORDER BY a.ordering'
		;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$html= '';

		if(count($rows)) {
			$html .='<h4>'.JText::_('J2STORE_PFILE_CURRENT_FILES').'</h4>';
		$html .='<table class="adminlist table table-striped" id="j2store_files_table">
			<thead>
			<th>'.JText::_('J2STORE_PLG_CONTENT_FILE_LABEL').'</th>
			<th>'.JText::_('J2STORE_PLG_CONTENT_MAX_DOWNLOADS').'</th>
			<th>'.JText::_('J2STORE_PLG_CONTENT_FILE_ENABLED').'</th>
			<th>'.JText::_('J2STORE_PLG_CONTENT_PURCHASE_NEEDED').'</th>
			</thead>
			<tbody>';
			foreach($rows as $row) {

				$html .='<tr>';
				$html .='<td>'.$row->product_file_display_name.'</td>';
				$html .='<td>'.(($row->download_limit==-1)?JText::_('J2STORE_PLG_CONTENT_UNLIMITED_DOWNLOADS'):$row->download_limit).'</td>';
				$state =($row->state)?JText::_('J2STORE_YES'):JText::_('J2STORE_NO');
				$html .='<td class="'.(($row->state)?'enabled':'disabled').'" >'.$state .' </td>';
				$purchase =($row->purchase_required)?JText::_('J2STORE_YES'):JText::_('J2STORE_NO');
				$html .='<td class="'.(($row->purchase_required)?'enabled':'disabled').'" >'.$purchase .' </td>';
				$html .='</tr>';
			}

			$html .='</tbody>';
			$html .='</table>';
		}
		return $html;
	}
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		parent::g_addProcess( 'afterStore', self::$type, $config, array( 'itemprice'=>$config['post']['item_price'], 'itemtax'=>$config['post']['item_tax'], 'specialprice'=>$config['post']['special_price'], 'itemshipping'=>$config['post']['item_shipping'], 'itemsku'=>$config['post']['item_sku'], 'productenabled'=>$config['post']['product_enabled'], 'itemmetrics'=>$config['post']['jform']['attribs']['item_metrics'], 'itemoptions'=>$config['post']['jform']['attribs']['item_options']  ) );
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
		
		// 
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderContent
	public static function onCCK_FieldBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
	}
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
	}
	
	// onCCK_FieldBeforeStore
	public static function onCCK_FieldBeforeStore( $process, &$fields, &$storages, &$config = array() )
	{
		
		var_dump($config);
	}
	
	// onCCK_FieldAfterStore
	public static function onCCK_FieldAfterStore( $process, &$fields, &$storages, &$config = array() )
	{
		
		$app = JFactory::getApplication();
	$articleId = $app->input->get('id', 0);
		// convert the joomla article attributes from json to object
		$db = JFactory::getDbo();
		$db->setQuery('SELECT COUNT(*) FROM #__j2store_prices WHERE article_id = '.$articleId);
		$res = $db->loadResult();
	if($res){
			//update query $process['
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__j2store_prices')
					->set('item_price='.$db->q($process['itemprice']))
					->set('item_tax='.$db->q($process['itemtax']))
					->set('special_price='. $db->q($process['specialprice']))
					->set('item_shipping='. $db->q($process['itemshipping']))
					->set('item_sku='. $db->q($process['itemsku']))
					->set('product_enabled='. $db->q($process['productenabled']))
					->where('article_id='.$db->q($articleId));
			$db->setQuery($query);

			if (!$db->query()) {
				throw new Exception($db->getErrorMsg());
			}

		}else{
			if($process['productenabled'] ==1){
				JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');
				$row = JTable::getInstance('prices','Table');

				$row->item_tax=$process['itemtax'];
				$row->item_price=$process['itemprice'];
				$row->special_price=$process['specialprice'];
				$row->item_shipping=$process['itemshipping'];
				$row->item_sku=$process['itemsku'];
				$row->product_enabled=$process['productenabled'];
				$row->article_id=$articleId;
				$articleId = $row->article_id;
				if ( !$row->save() )
				{
					$messagetype = 'notice';
					$message = JText::_( 'J2STORE_ERROR_SAVING_CHANGES' )." - ".$row->getError();
				}
			}

		}

			//save metrics
		self::_addMetrics($process, $articleId);

		//save options
		self::_addProductOptions($process, $articleId);
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
function _addProductOptions($process, $product_id) {
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');

		//get option IDs and save them as product option ids
		$pa_options = $process['itemoptions']['product_option_ids'];
		//get whether the option is required.
		$pa_option_required = $process['itemoptions']['product_option_required'];

		if(isset($pa_options) && count($pa_options) && isset($pa_option_required) ) {

			//convert option required values object to array
			$registry = new JRegistry;
			$registry->loadObject($pa_option_required );
			$pa_option_required = $registry->toArray();

			foreach ($pa_options as $option_id) {
				$table =  JTable::getInstance('ProductOptions', 'Table');
				//save this stock in the product quantities table.
				$table->product_id = $product_id;
				$table->option_id = $option_id;
				if($pa_option_required[$option_id]) {
					$table->required = 1;
				} else {
					$table->required = 0;
				}
				$table->store();
			}

		}

		//if user modified his option preferences, we got to get the changes and save them as well.
		$modified_option_required = $process['itemoptions']['product_option_required_save'];
		if(isset($modified_option_required) && count($modified_option_required ) ) {
			foreach($modified_option_required as $po_id=>$value) {
				$item =  JTable::getInstance('ProductOptions', 'Table');
				$item->load($po_id);
				$item->required = $value;
				$item->store();
			}
		}


	}

function _addMetrics($process, $product_id) {

		if(!empty($product_id) && $product_id > 0) {
		
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');
			$row = JTable::getInstance('Prices','Table');
			$row->load(array('article_id'=>$product_id));
				
			//save metrics
		//	if(isset($process['itemmetrics']) && is_object($process['itemmetrics'])) {
				if(isset($process['itemmetrics'])) {
				$data = 	$process['itemmetrics'];
				// $data = JArrayHelper::fromObject($metrics);

				$data['price_id'] = $row->price_id;
			//	var_dump($data);die();
				$row = JTable::getInstance('prices','Table');
				$row->bind($data);
				$row->store();
			}
		}


	}
	//
}
?>
