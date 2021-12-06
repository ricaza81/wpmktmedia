<?php
/* Drip integration for Layered Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class ulp_drip_class {
	var $default_popup_options = array(
		"drip_enable" => "off",
		"drip_api_token" => "",
		"drip_account" => "",
		"drip_account_id" => "",
		"drip_campaign" => "",
		"drip_campaign_id" => "",
		"drip_tags" => array(),
		"drip_fields" => ""
	);
	function __construct() {
		$this->default_popup_options['drip_fields'] = serialize(array('name' => '{subscription-name}'));
		if (is_admin()) {
			add_action('ulp_popup_options_integration_show', array(&$this, 'popup_options_show'));
			add_filter('ulp_popup_options_check', array(&$this, 'popup_options_check'), 10, 1);
			add_filter('ulp_popup_options_populate', array(&$this, 'popup_options_populate'), 10, 1);
			add_filter('ulp_popup_options_tabs', array(&$this, 'popup_options_tabs'), 10, 1);
			add_action('wp_ajax_ulp-drip-accounts', array(&$this, "show_accounts"));
			add_action('wp_ajax_ulp-drip-campaigns', array(&$this, "show_campaigns"));
		}
		add_action('ulp_subscribe', array(&$this, 'subscribe'), 10, 2);
	}
	function popup_options_tabs($_tabs) {
		if (!array_key_exists("integration", $_tabs)) $_tabs["integration"] = __('Integration', 'ulp');
		return $_tabs;
	}
	function popup_options_show($_popup_options) {
		$popup_options = array_merge($this->default_popup_options, $_popup_options);
		$fields = unserialize($popup_options['drip_fields']);
		echo '
				<h3>'.__('Drip Parameters', 'ulp').'</h3>
				<table class="ulp_useroptions">
					<tr>
						<th>'.__('Enable Drip', 'ulp').':</th>
						<td>
							<input type="checkbox" id="ulp_drip_enable" name="ulp_drip_enable" '.($popup_options['drip_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to Drip', 'ulp').'
							<br /><em>'.__('Please tick checkbox if you want to submit contact details to Drip.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('API Token', 'ulp').':</th>
						<td>
							<input type="text" id="ulp_drip_api_token" name="ulp_drip_api_token" value="'.esc_html($popup_options['drip_api_token']).'" class="widefat">
							<br /><em>'.__('Enter your Drip API Token. You can get it <a href="https://www.getdrip.com/user/edit" target="_blank">here</a>.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('Account ID:', 'ulp').'</th>
						<td>
							<input type="text" id="ulp-drip-account" name="ulp_drip_account" value="'.esc_html($popup_options['drip_account']).'" class="ulp-input-options ulp-input" readonly="readonly" onfocus="ulp_drip_accounts_focus(this);" onblur="ulp_input_options_blur(this);" />
							<input type="hidden" id="ulp-drip-account-id" name="ulp_drip_account_id" value="'.esc_html($popup_options['drip_account_id']).'" />
							<div id="ulp-drip-account-items" class="ulp-options-list">
								<div class="ulp-options-list-data"></div>
								<div class="ulp-options-list-spinner"></div>
							</div>
							<br /><em>'.__('Select Account ID.', 'ulp').'</em>
							<script>
								function ulp_drip_accounts_focus(object) {
									ulp_input_options_focus(object, {"action": "ulp-drip-accounts", "ulp_api_token": jQuery("#ulp_drip_api_token").val()});
								}
							</script>
						</td>
					</tr>
					<tr>
						<th>'.__('Campaign ID:', 'ulp').'</th>
						<td>
							<input type="text" id="ulp-drip-campaign" name="ulp_drip_campaign" value="'.esc_html($popup_options['drip_campaign']).'" class="ulp-input-options ulp-input" readonly="readonly" onfocus="ulp_drip_campaigns_focus(this);" onblur="ulp_input_options_blur(this);" />
							<input type="hidden" id="ulp-drip-campaign-id" name="ulp_drip_campaign_id" value="'.esc_html($popup_options['drip_campaign_id']).'" />
							<div id="ulp-drip-campaign-items" class="ulp-options-list">
								<div class="ulp-options-list-data"></div>
								<div class="ulp-options-list-spinner"></div>
							</div>
							<br /><em>'.__('Select Campaign ID.', 'ulp').'</em>
							<script>
								function ulp_drip_campaigns_focus(object) {
									ulp_input_options_focus(object, {"action": "ulp-drip-campaigns", "ulp_api_token": jQuery("#ulp_drip_api_token").val(), "ulp_account_id": jQuery("#ulp-drip-account-id").val()});
								}
							</script>
						</td>
					</tr>
					<tr>
						<th>'.__('Tags', 'ulp').':</th>
						<td>
							<input type="text" id="ulp_drip_tags_string" name="ulp_drip_tags_string" value="'.esc_html(implode(', ', $popup_options['drip_tags'])).'" class="widefat">
							<br /><em>'.__('Specify comma-separated list of tags that applies to subscribers.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('Custom Fields', 'ulp').':</th>
						<td>
							<table style="width: 100%;">
								<tr>
									<td style="width: 200px;"><strong>'.__('Name', 'ulp').'</strong></td>
									<td><strong>'.__('Value', 'ulp').'</strong></td>
								</tr>';
		$i = 0;
		if (is_array($fields)) {
			foreach ($fields as $key => $value) {
				echo '									
								<tr>
									<td>
										<input type="text" name="ulp_drip_fields_name[]" value="'.esc_html($key).'" class="widefat">
										<br /><em>'.($i > 0 ? '<a href="#" onclick="return ulp_drip_remove_fields(this);">'.__('Remove Field', 'ulp').'</a>' : '').'</em>
									</td>
									<td>
										<input type="text" name="ulp_drip_fields_value[]" value="'.esc_html($value).'" class="widefat">
									</td>
								</tr>';
				$i++;
			}
		}
		if ($i == 0) {
			echo '									
								<tr>
									<td>
										<input type="text" name="ulp_drip_fields_name[]" value="" class="widefat">
									</td>
									<td>
										<input type="text" name="ulp_drip_fields_value[]" value="" class="widefat">
									</td>
								</tr>';
		}
		echo '
								<tr style="display: none;" id="drip-fields-template">
									<td>
										<input type="text" name="ulp_drip_fields_name[]" value="" class="widefat">
										<br /><em><a href="#" onclick="return ulp_drip_remove_fields(this);">'.__('Remove Field', 'ulp').'</a></em>
									</td>
									<td>
										<input type="text" name="ulp_drip_fields_value[]" value="" class="widefat">
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<a href="#" class="button-secondary" onclick="return ulp_drip_add_fields(this);">'.__('Add Field', 'ulp').'</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<script>
					function ulp_drip_add_fields(object) {
						jQuery("#drip-fields-template").before("<tr>"+jQuery("#drip-fields-template").html()+"</tr>");
						return false;
					}
					function ulp_drip_remove_fields(object) {
						var row = jQuery(object).parentsUntil("tr").parent();
						jQuery(row).fadeOut(300, function() {
							jQuery(row).remove();
						});
						return false;
					}
				</script>';
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
		if (isset($ulp->postdata["ulp_drip_enable"])) $popup_options['drip_enable'] = "on";
		else $popup_options['drip_enable'] = "off";
		if ($popup_options['drip_enable'] == 'on') {
			if (empty($popup_options['drip_account_id'])) $errors[] = __('Invalid Drip Account ID.', 'ulp');
			if (empty($popup_options['drip_api_token'])) $errors[] = __('Invalid Drip API Token.', 'ulp');
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
		if (isset($ulp->postdata["ulp_drip_enable"])) $popup_options['drip_enable'] = "on";
		else $popup_options['drip_enable'] = "off";
		if (is_array($ulp->postdata["ulp_drip_fields_name"]) && is_array($ulp->postdata["ulp_drip_fields_value"])) {
			$fields = array();
			for($i=0; $i<sizeof($ulp->postdata["ulp_drip_fields_name"]); $i++) {
				$key = stripslashes(trim($ulp->postdata['ulp_drip_fields_name'][$i]));
				$value = stripslashes(trim($ulp->postdata['ulp_drip_fields_value'][$i]));
				if (!empty($key)) $fields[$key] = $value;
			}
			if (!empty($fields)) $popup_options['drip_fields'] = serialize($fields);
			else $popup_options['drip_fields'] = '';
		} else $popup_options['drip_fields'] = '';
		$popup_options['drip_tags'] = array();
		if (isset($ulp->postdata["ulp_drip_tags_string"])) {
			$items = explode(',', $ulp->postdata["ulp_drip_tags_string"]);
			$tags = array();
			foreach ($items as $item) {
				$item = trim($item);
				if (strlen($item) > 0) $popup_options['drip_tags'][] = $item;
			}
		}
		return array_merge($_popup_options, $popup_options);
	}
	function show_accounts() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (!isset($_POST['ulp_api_token']) || empty($_POST['ulp_api_token'])) {
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Invalid API Token!', 'ulp').'</div>';
				echo json_encode($return_object);
				exit;
			}
			$token = trim(stripslashes($_POST['ulp_api_token']));
			$accounts = array();
			try {
				$curl = curl_init('https://api.getdrip.com/v2/accounts');
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_USERPWD, $token);
				
				$response = curl_exec($curl);
				curl_close($curl);
				$result = json_decode($response, true);
				if ($result && array_key_exists('accounts', $result)) {
					if (is_array($result['accounts']) && sizeof($result['accounts']) > 0) {
						foreach ($result['accounts'] as $account) {
							if (is_array($account)) {
								if (array_key_exists('id', $account) && array_key_exists('name', $account)) {
									$accounts[$account['id']] = $account['name'];
								}
							}
						}
					} else {
						$return_object = array();
						$return_object['status'] = 'OK';
						$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('No accounts found!', 'ulp').'</div>';
						echo json_encode($return_object);
						exit;
					}
				} else {
					$return_object = array();
					$return_object['status'] = 'OK';
					$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Invalid API Token!', 'ulp').'</div>';
					echo json_encode($return_object);
					exit;
				}
			} catch (Exception $e) {
			}
			$list_html = '';
			if (!empty($accounts)) {
				foreach ($accounts as $id => $name) {
					$list_html .= '<a href="#" data-id="'.esc_html($id).'" data-title="'.esc_html($id).(!empty($name) ? ' | '.esc_html($name) : '').'" onclick="return ulp_input_options_selected(this);">'.esc_html($id).(!empty($name) ? ' | '.esc_html($name) : '').'</a>';
				}
			} else $list_html .= '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'ulp').'</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $list_html;
			$return_object['items'] = sizeof($accounts);
			echo json_encode($return_object);
		}
		exit;
	}
	function show_campaigns() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (!isset($_POST['ulp_api_token']) || empty($_POST['ulp_api_token']) || !isset($_POST['ulp_account_id']) || empty($_POST['ulp_account_id'])) {
				$return_object = array();
				$return_object['status'] = 'OK';
				$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Invalid API Token or Account ID!', 'ulp').'</div>';
				echo json_encode($return_object);
				exit;
			}
			$token = trim(stripslashes($_POST['ulp_api_token']));
			$account = trim(stripslashes($_POST['ulp_account_id']));
			$campaigns = array();
			try {
				$curl = curl_init('https://api.getdrip.com/v2/'.$account.'/campaigns');
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_USERPWD, $token);
				
				$response = curl_exec($curl);
				curl_close($curl);
				$result = json_decode($response, true);
				if ($result && array_key_exists('campaigns', $result)) {
					if (is_array($result['campaigns']) && sizeof($result['campaigns']) > 0) {
						foreach ($result['campaigns'] as $campaign) {
							if (is_array($campaign)) {
								if (array_key_exists('id', $campaign) && array_key_exists('name', $campaign)) {
									$campaigns[$campaign['id']] = $campaign['name'];
								}
							}
						}
					} else {
						$return_object = array();
						$return_object['status'] = 'OK';
						$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('No campaigns found!', 'ulp').'</div>';
						echo json_encode($return_object);
						exit;
					}
				} else {
					$return_object = array();
					$return_object['status'] = 'OK';
					$return_object['html'] = '<div style="text-align: center; margin: 20px 0px;">'.__('Invalid API Token or Account ID!', 'ulp').'</div>';
					echo json_encode($return_object);
					exit;
				}
			} catch (Exception $e) {
			}
			$list_html = '';
			if (!empty($campaigns)) {
				foreach ($campaigns as $id => $name) {
					$list_html .= '<a href="#" data-id="'.esc_html($id).'" data-title="'.esc_html($id).(!empty($name) ? ' | '.esc_html($name) : '').'" onclick="return ulp_input_options_selected(this);">'.esc_html($id).(!empty($name) ? ' | '.esc_html($name) : '').'</a>';
				}
			} else $list_html .= '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'ulp').'</div>';
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = $list_html;
			$return_object['items'] = sizeof($campaigns);
			echo json_encode($return_object);
		}
		exit;
	}
	function subscribe($_popup_options, $_subscriber) {
		if (empty($_subscriber['{subscription-email}'])) return;
		$popup_options = array_merge($this->default_popup_options, $_popup_options);
		if ($popup_options['drip_enable'] == 'on') {
			$data = array(
				'email' => $_subscriber['{subscription-email}'], 
				'ip_address' => $_SERVER['REMOTE_ADDR']
			);
			$fields = unserialize($popup_options['drip_fields']);
			if (is_array($fields)) {
				foreach ($fields as $key => $value) {
					$data['custom_fields'][$key] = strtr($value, $_subscriber);
				}
			}
			if (!empty($popup_options['drip_tags'])) $data['tags'] = $popup_options['drip_tags'];

			try {
				$curl = curl_init('https://api.getdrip.com/v2/'.$popup_options['drip_account_id'].'/subscribers');
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.api+json'));
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_USERPWD, $popup_options['drip_api_token']);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array("subscribers" => array($data))));
				$response = curl_exec($curl);
				curl_close($curl);
				
				$curl = curl_init('https://api.getdrip.com/v2/'.$popup_options['drip_account_id'].'/campaigns/'.$popup_options['drip_campaign_id'].'/subscribers');
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.api+json'));
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_USERPWD, $popup_options['drip_api_token']);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array("subscribers" => array(array('email' => $_subscriber['{subscription-email}'])))));
				$response = curl_exec($curl);
				curl_close($curl);
			} catch (Exception $e) {
			}
		}
	}
}
$ulp_drip = new ulp_drip_class();
?>