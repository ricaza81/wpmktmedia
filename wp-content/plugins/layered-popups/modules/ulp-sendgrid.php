<?php
/* SendGrid integration for Layered Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class ulp_sendgrid_class {
	var $default_popup_options = array(
		"sendgrid_enable" => "off",
		"sendgrid_api_key" => "",
		"sendgrid_list" => "",
		"sendgrid_list_id" => "",
		"sendgrid_email" => "{subscription-email}",
		"sendgrid_first_name" => "{subscription-name}",
		"sendgrid_last_name" => "",
		"sendgrid_fields" => array()
	);
	function __construct() {
		if (is_admin()) {
			add_action('ulp_popup_options_integration_show', array(&$this, 'popup_options_show'));
			add_filter('ulp_popup_options_check', array(&$this, 'popup_options_check'), 10, 1);
			add_filter('ulp_popup_options_populate', array(&$this, 'popup_options_populate'), 10, 1);
			add_action('wp_ajax_ulp-sendgrid-lists', array(&$this, "show_lists"));
			add_action('wp_ajax_ulp-sendgrid-fields', array(&$this, "show_fields"));
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
				<h3>'.__('SendGrid Parameters', 'ulp').'</h3>
				<table class="ulp_useroptions">
					<tr>
						<th>'.__('Enable SendGrid', 'ulp').':</th>
						<td>
							<input type="checkbox" id="ulp_sendgrid_enable" name="ulp_sendgrid_enable" '.($popup_options['sendgrid_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to SendGrid', 'ulp').'
							<br /><em>'.__('Please tick checkbox if you want to submit contact details to SendGrid.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('API Key', 'ulp').':</th>
						<td>
							<input type="text" id="ulp_sendgrid_api_key" name="ulp_sendgrid_api_key" value="'.esc_html($popup_options['sendgrid_api_key']).'" class="widefat">
							<br /><em>'.__('Enter your SendGrid API Key. You can get it <a href="https://app.sendgrid.com/settings/api_keys" target="_blank">here</a>. Make sure that API Key has Full Access to Marketing Campaigns.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('List ID', 'ulp').':</th>
						<td>
							<input type="text" id="ulp-sendgrid-list" name="ulp_sendgrid_list" value="'.esc_html($popup_options['sendgrid_list']).'" class="ulp-input-options ulp-input" readonly="readonly" onfocus="ulp_sendgrid_lists_focus(this);" onblur="ulp_input_options_blur(this);" />
							<input type="hidden" id="ulp-sendgrid-list-id" name="ulp_sendgrid_list_id" value="'.esc_html($popup_options['sendgrid_list_id']).'" />
							<div id="ulp-sendgrid-list-items" class="ulp-options-list">
								<div class="ulp-options-list-data"></div>
								<div class="ulp-options-list-spinner"></div>
							</div>
							<br /><em>'.__('Enter your List ID.', 'ulp').'</em>
							<script>
								function ulp_sendgrid_lists_focus(object) {
									ulp_input_options_focus(object, {"action": "ulp-sendgrid-lists", "ulp_api_key": jQuery("#ulp_sendgrid_api_key").val()});
								}
							</script>
						</td>
					</tr>
					<tr>
						<th>'.__('Fields', 'ulp').':</th>
						<td style="vertical-align: middle;">
							'.__('Please adjust the fields below. You can use the same shortcodes (<code>{subscription-email}</code>, <code>{subscription-name}</code>, etc.) to associate SendGrid fields with the popup fields.', 'ulp').'
							<table style="min-width: 280px; width: 50%;">
								<tr>
									<td style="width: 100px;"><strong>'.__('email').':</strong></td>
									<td>
										<input type="text" id="ulp_sendgrid_email" name="ulp_sendgrid_email" value="{subscription-email}" class="widefat" readonly="readonly" />
										<br /><em>'.__('Email address').'</em>
									</td>
								</tr>
								<tr>
									<td style="width: 100px;"><strong>'.__('first_name').':</strong></td>
									<td>
										<input type="text" id="ulp_sendgrid_first_name" name="ulp_sendgrid_first_name" value="'.esc_html($popup_options['sendgrid_first_name']).'" class="widefat" />
										<br /><em>'.__('First name').'</em>
									</td>
								</tr>
								<tr>
									<td style="width: 100px;"><strong>'.__('last_name').':</strong></td>
									<td>
										<input type="text" id="ulp_sendgrid_last_name" name="ulp_sendgrid_last_name" value="'.esc_html($popup_options['sendgrid_last_name']).'" class="widefat" />
										<br /><em>'.__('Last name').'</em>
									</td>
								</tr>
							</table>
							<div class="ulp-sendgrid-fields-html">';
		if (!empty($popup_options['sendgrid_api_key'])) {
			$fields = $this->get_fields_html($popup_options['sendgrid_api_key'], $popup_options['sendgrid_fields']);
			echo $fields;
		}
		echo '
							</div>
							<a id="ulp_sendgrid_fields_button" class="ulp_button button-secondary" onclick="return ulp_sendgrid_loadfields();">'.__('Load Custom Fields', 'ulp').'</a>
							<img class="ulp-loading" id="ulp-sendgrid-fields-loading" src="'.plugins_url('/images/loading.gif', dirname(__FILE__)).'">
							<br /><em>'.__('Click the button to (re)load fields list. Ignore if you do not need specify fields values.', 'ulp').'</em>
							<script>
								function ulp_sendgrid_loadfields() {
									jQuery("#ulp-sendgrid-fields-loading").fadeIn(350);
									jQuery(".ulp-sendgrid-fields-html").slideUp(350);
									var data = {action: "ulp-sendgrid-fields", ulp_key: jQuery("#ulp_sendgrid_api_key").val()};
									jQuery.post("'.admin_url('admin-ajax.php').'", data, function(return_data) {
										jQuery("#ulp-sendgrid-fields-loading").fadeOut(350);
										try {
											var data = jQuery.parseJSON(return_data);
											var status = data.status;
											if (status == "OK") {
												jQuery(".ulp-sendgrid-fields-html").html(data.html);
												jQuery(".ulp-sendgrid-fields-html").slideDown(350);
											} else {
												jQuery(".ulp-sendgrid-fields-html").html("<div class=\'ulp-sendgrid-grouping\' style=\'margin-bottom: 10px;\'><strong>'.__('Internal error! Can not connect to SendGrid server.', 'ulp').'</strong></div>");
												jQuery(".ulp-sendgrid-fields-html").slideDown(350);
											}
										} catch(error) {
											jQuery(".ulp-sendgrid-fields-html").html("<div class=\'ulp-sendgrid-grouping\' style=\'margin-bottom: 10px;\'><strong>'.__('Internal error! Can not connect to SendGrid server.', 'ulp').'</strong></div>");
											jQuery(".ulp-sendgrid-fields-html").slideDown(350);
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
		if (isset($ulp->postdata["ulp_sendgrid_enable"])) $popup_options['sendgrid_enable'] = "on";
		else $popup_options['sendgrid_enable'] = "off";
		if ($popup_options['sendgrid_enable'] == 'on') {
			if (empty($popup_options['sendgrid_api_key'])) $errors[] = __('Invalid SendGrid API Key.', 'ulp');
			if (empty($popup_options['sendgrid_list_id'])) $errors[] = __('Invalid SendGrid List ID.', 'ulp');
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
		if (isset($ulp->postdata["ulp_sendgrid_enable"])) $popup_options['sendgrid_enable'] = "on";
		else $popup_options['sendgrid_enable'] = "off";
		
		$fields = array();
		foreach($ulp->postdata as $key => $value) {
			if (substr($key, 0, strlen('ulp_sendgrid_field_')) == 'ulp_sendgrid_field_') {
				$field = substr($key, strlen('ulp_sendgrid_field_'));
				$fields[$field] = stripslashes(trim($value));
			}
		}
		$popup_options['sendgrid_fields'] = $fields;
		
		return array_merge($_popup_options, $popup_options);
	}
	function subscribe($_popup_options, $_subscriber) {
		if (empty($_subscriber['{subscription-email}'])) return;
		$popup_options = array_merge($this->default_popup_options, $_popup_options);
		if ($popup_options['sendgrid_enable'] == 'on') {
			$data = array(
				'email' => $_subscriber['{subscription-email}'],
				'first_name' => strtr($popup_options['sendgrid_first_name'], $_subscriber),
				'last_name' => strtr($popup_options['sendgrid_last_name'], $_subscriber)
			);
			foreach ($popup_options['sendgrid_fields'] as $key => $value) {
				if (!empty($value)) {
					$data[$key] = strtr($value, $_subscriber);
				}
			}
			$result = $this->connect($popup_options['sendgrid_api_key'], 'recipients/search?email='.strtolower($_subscriber['{subscription-email}']));
			if (is_array($result) && array_key_exists('recipients', $result)) {
				$subscriber_id = '';
				if (sizeof($result['recipients']) > 0) {
					$subscriber_id = $result['recipients'][0]['id'];
					$result = $this->connect($popup_options['sendgrid_api_key'], 'recipients', array($data), 'PATCH');
				} else {
					$result = $this->connect($popup_options['sendgrid_api_key'], 'recipients', array($data));
					if (is_array($result) && array_key_exists('persisted_recipients', $result)) {
						$subscriber_id = $result['persisted_recipients'][0];
					}
				}
				if (!empty($subscriber_id)) {
					$result = $this->connect($popup_options['sendgrid_api_key'], 'lists/'.$popup_options['sendgrid_list_id'].'/recipients/'.$subscriber_id, array($data));
				}
			}
		}
	}
	function show_lists() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$lists = array();
			if (!isset($_POST['ulp_api_key']) || empty($_POST['ulp_api_key'])) {
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Invalid API Key!', 'ulp').'</div>';
				echo json_encode($return_object);
				exit;
			}
			$key = trim(stripslashes($_POST['ulp_api_key']));
			
			$result = $this->connect($key, 'lists');
			if (is_array($result)) {
				if (array_key_exists('errors', $result)) {
					$return_object = array();
					$return_object['status'] = 'OK';
					$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.ucfirst($result['errors'][0]['message']).'.</div>';
					echo json_encode($return_object);
					exit;
				} else if (!array_key_exists('lists', $result)) {
						$return_object = array();
						$return_object['status'] = 'OK';
						$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Invalid server response!', 'ulp').'</div>';
						echo json_encode($return_object);
						exit;
				} else {
					if (sizeof($result['lists']) > 0) {
						foreach ($result['lists'] as $list) {
							if (is_array($list)) {
								if (array_key_exists('id', $list) && array_key_exists('name', $list)) {
									$lists[$list['id']] = $list['name'];
								}
							}
						}
					} else {
						$return_object = array();
						$return_object['status'] = 'OK';
						$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('No Lists found!', 'ulp').'</div>';
						echo json_encode($return_object);
						exit;
					}
				}
			} else {
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Invalid API Key!', 'ulp').'</div>';
				echo json_encode($return_object);
				exit;
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
			if (!isset($_POST['ulp_key']) || empty($_POST['ulp_key'])) {
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['html'] = '<div class="ulp-sendgrid-grouping" style="margin-bottom: 10px;"><strong>'.__('Invalid API Key.', 'ulp').'</strong></div>';
				echo json_encode($return_object);
				exit;
			}
			$key = trim(stripslashes($_POST['ulp_key']));
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $this->get_fields_html($key, $this->default_popup_options['sendgrid_fields']);
			echo json_encode($return_object);
		}
		exit;
	}
	function get_fields_html($_key, $_fields) {
		$result = $this->connect($_key, 'custom_fields');
		$fields = '';
		if (is_array($result)) {
			if (array_key_exists('errors', $result)) {
				$fields = '<div class="ulp-sendgrid-grouping" style="margin-bottom: 10px;"><strong>'.ucfirst($result['errors'][0]['message']).'.</strong></div>';
			} else if (!array_key_exists('custom_fields', $result)) {
				$fields = '<div class="ulp-sendgrid-grouping" style="margin-bottom: 10px;"><strong>'.__('Invalid server response!', 'ulp').'</strong></div>';
			} else {
				if (sizeof($result['custom_fields']) > 0) {
					$fields = '
			<table style="min-width: 280px; width: 50%;">';
					foreach ($result['custom_fields'] as $field) {
						if (is_array($field)) {
							if (array_key_exists('name', $field)) {
								$fields .= '
				<tr>
					<td style="width: 100px;"><strong>'.esc_html($field['name']).':</strong></td>
					<td>
						<input type="text" id="ulp_sendgrid_field_'.esc_html($field['name']).'" name="ulp_sendgrid_field_'.esc_html($field['name']).'" value="'.esc_html(array_key_exists($field['name'], $_fields) ? $_fields[$field['name']] : '').'" class="widefat" />
						<br /><em>'.esc_html($field['name']).'</em>
					</td>
				</tr>';
							}
						}
					}
					$fields .= '
			</table>';
				} else {
					$fields = '<div class="ulp-sendgrid-grouping" style="margin-bottom: 10px;"><strong>'.__('No custom fields found.', 'ulp').'</strong></div>';
				}
			}
		} else {
			$fields = '<div class="ulp-sendgrid-grouping" style="margin-bottom: 10px;"><strong>'.__('Inavlid server response.', 'ulp').'</strong></div>';
		}
		return $fields;
	}
	function connect($_api_key, $_path, $_data = array(), $_method = '') {
		$headers = array(
			'Authorization: Bearer '.$_api_key,
			'Content-Type: application/json',
			'Accept: application/json'
		);
		try {
			$url = 'https://api.sendgrid.com/v3/contactdb/'.ltrim($_path, '/');
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if (!empty($_data)) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($_data));
			}
			if (!empty($_method)) {
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_method);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 120);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
			//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			curl_close($curl);
			$result = json_decode($response, true);
		} catch (Exception $e) {
			$result = false;
		}
		return $result;
	}
}
$ulp_sendgrid = new ulp_sendgrid_class();
?>