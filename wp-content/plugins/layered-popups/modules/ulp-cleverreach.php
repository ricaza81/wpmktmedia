<?php
/* CleverReach integration for Layered Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class ulp_cleverreach_class {
	var $default_popup_options = array(
		"cleverreach_enable" => "off",
		"cleverreach_api_key" => "",
		"cleverreach_list" => "",
		"cleverreach_list_id" => "",
		"cleverreach_fields" => ""
	);
	function __construct() {
		$this->default_popup_options['cleverreach_fields'] = serialize(array('firstname' => '{subscription-name}', 'first_name' => '{subscription-name}', 'name' => '{subscription-name}'));
		if (is_admin()) {
			add_action('ulp_popup_options_integration_show', array(&$this, 'popup_options_show'));
			add_filter('ulp_popup_options_check', array(&$this, 'popup_options_check'), 10, 1);
			add_filter('ulp_popup_options_populate', array(&$this, 'popup_options_populate'), 10, 1);
			add_action('wp_ajax_ulp-cleverreach-lists', array(&$this, "show_lists"));
			add_action('wp_ajax_ulp-cleverreach-fields', array(&$this, "show_fields"));
			add_filter('ulp_popup_options_tabs', array(&$this, 'popup_options_tabs'), 10, 1);
		}
		add_action('ulp_subscribe', array(&$this, 'subscribe'), 10, 2);
	}
	function popup_options_tabs($_tabs) {
		if (!array_key_exists("integration", $_tabs)) $_tabs["integration"] = __('Integration', 'ulp');
		return $_tabs;
	}
	function popup_options_show($_popup_options) {
		$popup_options = array_merge($this->default_popup_options, $_popup_options);
		echo '
				<h3>'.__('CleverReach Parameters', 'ulp').'</h3>
				<table class="ulp_useroptions">
					<tr>
						<th>'.__('Enable CleverReach', 'ulp').':</th>
						<td>
							<input type="checkbox" id="ulp_cleverreach_enable" name="ulp_cleverreach_enable" '.($popup_options['cleverreach_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to CleverReach', 'ulp').'
							<br /><em>'.__('Please tick checkbox if you want to submit contact details to CleverReach.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('API Key', 'ulp').':</th>
						<td>
							<input type="text" id="ulp_cleverreach_api_key" name="ulp_cleverreach_api_key" value="'.esc_html($popup_options['cleverreach_api_key']).'" class="widefat">
							<br /><em>'.__('Enter your CleverReach SOAP API Key. In your CleverReach account click "Account", then "Extras", then "API".', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('List ID:', 'ulp').'</th>
						<td>
							<input type="text" id="ulp-cleverreach-list" name="ulp_cleverreach_list" value="'.esc_html($popup_options['cleverreach_list']).'" class="ulp-input-options ulp-input" readonly="readonly" onfocus="ulp_cleverreach_lists_focus(this);" onblur="ulp_input_options_blur(this);" />
							<input type="hidden" id="ulp-cleverreach-list-id" name="ulp_cleverreach_list_id" value="'.esc_html($popup_options['cleverreach_list_id']).'" />
							<div id="ulp-cleverreach-list-items" class="ulp-options-list">
								<div class="ulp-options-list-data"></div>
								<div class="ulp-options-list-spinner"></div>
							</div>
							<br /><em>'.__('Enter your List ID.', 'ulp').'</em>
							<script>
								function ulp_cleverreach_lists_focus(object) {
									ulp_input_options_focus(object, {"action": "ulp-cleverreach-lists", "ulp_api_key": jQuery("#ulp_cleverreach_api_key").val()});
								}
							</script>
						</td>
					</tr>
					<tr>
						<th>'.__('Fields', 'ulp').':</th>
						<td style="vertical-align: middle;">
							<div class="ulp-cleverreach-fields-html">';
		if (!empty($popup_options['cleverreach_api_key']) && !empty($popup_options['cleverreach_list_id'])) {
			$fields = $this->get_fields_html($popup_options['cleverreach_api_key'], $popup_options['cleverreach_list_id'], $popup_options['cleverreach_fields']);
			echo $fields;
		}
		echo '
							</div>
							<a id="ulp_cleverreach_fields_button" class="ulp_button button-secondary" onclick="return ulp_cleverreach_loadfields();">'.__('Load Fields', 'ulp').'</a>
							<img class="ulp-loading" id="ulp-cleverreach-fields-loading" src="'.plugins_url('/images/loading.gif', dirname(__FILE__)).'">
							<br /><em>'.__('Click the button to (re)load fields list. Ignore if you do not need specify fields values.', 'ulp').'</em>
							<script>
								function ulp_cleverreach_loadfields() {
									jQuery("#ulp-cleverreach-fields-loading").fadeIn(350);
									jQuery(".ulp-cleverreach-fields-html").slideUp(350);
									var data = {action: "ulp-cleverreach-fields", ulp_key: jQuery("#ulp_cleverreach_api_key").val(), ulp_list: jQuery("#ulp-cleverreach-list-id").val()};
									jQuery.post("'.admin_url('admin-ajax.php').'", data, function(return_data) {
										jQuery("#ulp-cleverreach-fields-loading").fadeOut(350);
										try {
											var data = jQuery.parseJSON(return_data);
											var status = data.status;
											if (status == "OK") {
												jQuery(".ulp-cleverreach-fields-html").html(data.html);
												jQuery(".ulp-cleverreach-fields-html").slideDown(350);
											} else {
												jQuery(".ulp-cleverreach-fields-html").html("<div class=\'ulp-cleverreach-grouping\' style=\'margin-bottom: 10px;\'><strong>'.__('Internal error! Can not connect to CleverReach server.', 'ulp').'</strong></div>");
												jQuery(".ulp-cleverreach-fields-html").slideDown(350);
											}
										} catch(error) {
											jQuery(".ulp-cleverreach-fields-html").html("<div class=\'ulp-cleverreach-grouping\' style=\'margin-bottom: 10px;\'><strong>'.__('Internal error! Can not connect to CleverReach server.', 'ulp').'</strong></div>");
											jQuery(".ulp-cleverreach-fields-html").slideDown(350);
										}
									});
									return false;
								}
							</script>
						</td>
					</tr>
				</table>';
	}
	function popup_options_check($_errors) {
		global $ulp;
		$errors = array();
		$popup_options = array();
		foreach ($this->default_popup_options as $key => $value) {
			if (isset($ulp->postdata['ulp_'.$key])) {
				$popup_options[$key] = stripslashes(trim($ulp->postdata['ulp_'.$key]));
			}
		}
		if (isset($ulp->postdata["ulp_cleverreach_enable"])) $popup_options['cleverreach_enable'] = "on";
		else $popup_options['cleverreach_enable'] = "off";
		if ($popup_options['cleverreach_enable'] == 'on') {
			if (empty($popup_options['cleverreach_api_key'])) $errors[] = __('Invalid CleverReach API Key.', 'ulp');
			if (empty($popup_options['cleverreach_list_id'])) $errors[] = __('Invalid CleverReach List ID.', 'ulp');
		}
		return array_merge($_errors, $errors);
	}
	function popup_options_populate($_popup_options) {
		global $ulp;
		$popup_options = array();
		foreach ($this->default_popup_options as $key => $value) {
			if (isset($ulp->postdata['ulp_'.$key])) {
				$popup_options[$key] = stripslashes(trim($ulp->postdata['ulp_'.$key]));
			}
		}
		if (isset($ulp->postdata["ulp_cleverreach_enable"])) $popup_options['cleverreach_enable'] = "on";
		else $popup_options['cleverreach_enable'] = "off";
		
		$fields = array();
		foreach($ulp->postdata as $key => $value) {
			if (substr($key, 0, strlen('ulp_cleverreach_field_')) == 'ulp_cleverreach_field_') {
				$field = substr($key, strlen('ulp_cleverreach_field_'));
				$fields[$field] = stripslashes(trim($value));
			}
		}
		$popup_options['cleverreach_fields'] = serialize($fields);
		
		return array_merge($_popup_options, $popup_options);
	}
	function subscribe($_popup_options, $_subscriber) {
		if (empty($_subscriber['{subscription-email}'])) return;
		$popup_options = array_merge($this->default_popup_options, $_popup_options);
		if ($popup_options['cleverreach_enable'] == 'on') {
			$data = array('properties' => array());
			$user = array(
				"email" => $_subscriber['{subscription-email}'],
				"registered" => time(),
				"activated" => time(),
				"source" => $popup_options['title']
			);
			$fields = array();
			if (!empty($popup_options['cleverreach_fields'])) $fields = unserialize($popup_options['cleverreach_fields']);
			if (!empty($fields) && is_array($fields)) {
				foreach ($fields as $key => $value) {
					if (!empty($value)) {
						$user['attributes'][] = array('key' => $key, 'value' => strtr($value, $_subscriber));
					}
				}
			}
			$wsdl_url = 'http://api.cleverreach.com/soap/interface_v5.1.php?wsdl';
			try {
				$api = new SoapClient($wsdl_url);
				$result = $api->receiverGetByEmail($popup_options['cleverreach_api_key'], $popup_options['cleverreach_list_id'], $_subscriber['{subscription-email}'], 1);
				if (is_object($result)) {
					if ($result->status == 'ERROR') {
						$result = $api->receiverAdd($popup_options['cleverreach_api_key'], $popup_options['cleverreach_list_id'], $user);
					} else {
						$result = $api->receiverUpdate($popup_options['cleverreach_api_key'], $popup_options['cleverreach_list_id'], $user);
					}
				}
				
				
			} catch (Exception $e) {
			}
		}
	}
	function show_lists() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (!isset($_POST['ulp_api_key']) || empty($_POST['ulp_api_key'])) {
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Invalid API Key!', 'ulp').'</div>';
				echo json_encode($return_object);
				exit;
			}
			$key = trim(stripslashes($_POST['ulp_api_key']));
			$wsdl_url = 'http://api.cleverreach.com/soap/interface_v5.1.php?wsdl';

			$lists = array();
			try {
				$api = new SoapClient($wsdl_url);
				$result = $api->groupGetList($key);			
				if (is_object($result)) {
					if ($result->status == 'ERROR') {
						$return_object = array();
						$return_object['status'] = 'OK';
						$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.esc_html(ucfirst($result->message)).'</div>';
						echo json_encode($return_object);
						exit;
					} else if (!is_array($result->data) || sizeof($result->data) == 0) {
						$return_object = array();
						$return_object['status'] = 'OK';
						$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Lists not found!', 'ulp').'</div>';
						echo json_encode($return_object);
						exit;
					}
					foreach($result->data as $list) {
						if (is_object($list)) {
							if (property_exists($list, 'id') && property_exists($list, 'name')) {
								$lists[$list->id] = $list->name;
							}
						}
					}
				} else {
					$return_object = array();
					$return_object['status'] = 'OK';
					$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Can not connect to CleverReach API server!', 'ulp').'</div>';
					echo json_encode($return_object);
					exit;
				}
			} catch (Exception $e) {
			}
			$list_html = '';
			if (!empty($lists)) {
				foreach ($lists as $id => $name) {
					$list_html .= '<a href="#" data-id="'.esc_html($id).'" data-title="'.esc_html($id).(!empty($name) ? ' | '.esc_html($name) : '').'" onclick="return ulp_input_options_selected(this);">'.esc_html($id).(!empty($name) ? ' | '.esc_html($name) : '').'</a>';
				}
			} else $list_html .= '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'ulp').'</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $list_html;
			$return_object['items'] = sizeof($lists);
			echo json_encode($return_object);
		}
		exit;
	}
	function show_fields() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$return_object = array();
			$return_object['status'] = 'OK';
			if (!isset($_POST['ulp_key']) || !isset($_POST['ulp_list']) || empty($_POST['ulp_key']) || empty($_POST['ulp_list'])) {
				$return_object['html'] = '<strong>'.__('Invalid API Key or List ID!', 'ulp').'</strong>';
			} else {
				$key = trim(stripslashes($_POST['ulp_key']));
				$list = trim(stripslashes($_POST['ulp_list']));
				$return_object['html'] = $this->get_fields_html($key, $list, $this->default_popup_options['cleverreach_fields']);
			}
			echo json_encode($return_object);
		}
		exit;
	}
	function get_fields_html($_key, $_list, $_fields) {
		$wsdl_url = 'http://api.cleverreach.com/soap/interface_v5.1.php?wsdl';
		$fields = '';
		try {
			$api = new SoapClient($wsdl_url);
			$result = $api->groupGetDetails($_key, $_list);
			if (is_object($result) && $result->status == 'SUCCESS') {
				$values = unserialize($_fields);
				if (!is_array($values)) $values = array();
				if (is_object($result->data) && (sizeof($result->data->attributes) != 0 || sizeof($result->data->globalAttributes) != 0)) {
					$fields = '
					'.__('Please adjust the fields below. You can use the same shortcodes (<code>{subscription-email}</code>, <code>{subscription-name}</code>, etc.) to associate CleverReach fields with the popup fields.', 'ulp').'
					<table style="min-width: 280px; width: 50%;">';
					$attributes = array_merge($result->data->attributes, $result->data->globalAttributes);
					foreach ($attributes as $field) {
						if (is_object($field)) {
							if (property_exists($field, 'key')) {
								$fields .= '
						<tr>
							<td style="width: 100px;"><strong>'.esc_html($field->key).':</strong></td>
							<td>
								<input type="text" id="ulp_cleverreach_field_'.esc_html($field->key).'" name="ulp_cleverreach_field_'.esc_html($field->key).'" value="'.esc_html(array_key_exists($field->key, $values) ? $values[$field->key] : '').'" class="widefat" />
								<br /><em>'.esc_html($field->key).'</em>
							</td>
						</tr>';
							}
						}
					}
							$fields .= '
					</table>';
				} else {
					$fields = '<div class="ulp-cleverreach-grouping" style="margin-bottom: 10px;"><strong>'.__('No fields found.', 'ulp').'</strong></div>';
				}
			} else {
				return '<div class="ulp-cleverreach-grouping" style="margin-bottom: 10px;"><strong>'.__('Can not connect to CleverReach API server!', 'ulp').'</strong></div>';
			}
		} catch (Exception $e) {
		}
		return $fields;
	}
}
$ulp_cleverreach = new ulp_cleverreach_class();
?>