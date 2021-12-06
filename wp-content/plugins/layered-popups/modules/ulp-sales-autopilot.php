<?php
/* SalesAutoPilot integration for Layered Popups */
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
class ulp_salesautopilot_class {
	var $default_popup_options = array(
		'salesautopilot_enable' => "off",
		'salesautopilot_username' => '',
		'salesautopilot_password' => '',
		'salesautopilot_list_id' => '',
		'salesautopilot_form_id' => ''
	);
	function __construct() {
		if (is_admin()) {
			add_action('ulp_popup_options_integration_show', array(&$this, 'popup_options_show'));
			add_filter('ulp_popup_options_check', array(&$this, 'popup_options_check'), 10, 1);
			add_filter('ulp_popup_options_populate', array(&$this, 'popup_options_populate'), 10, 1);
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
				<h3>'.__('SalesAutoPilot Parameters', 'ulp').'</h3>
				<table class="ulp_useroptions">
					<tr>
						<th>'.__('Enable SalesAutoPilot', 'ulp').':</th>
						<td>
							<input type="checkbox" id="ulp_salesautopilot_enable" name="ulp_salesautopilot_enable" '.($popup_options['salesautopilot_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to SalesAutoPilot', 'ulp').'
							<br /><em>'.__('Please tick checkbox if you want to submit contact details to SalesAutoPilot.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('API Username', 'ulp').':</th>
						<td>
							<input type="text" id="ulp_salesautopilot_username" name="ulp_salesautopilot_username" value="'.esc_html($popup_options['salesautopilot_username']).'" class="widefat">
							<br /><em>'.__('Enter your SalesAutoPilot API Username. You can get your API Key from the SalesAutoPilot account: Settings >> Integration >> API Keys.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('API Password', 'ulp').':</th>
						<td>
							<input type="text" id="ulp_salesautopilot_password" name="ulp_salesautopilot_password" value="'.esc_html($popup_options['salesautopilot_password']).'" class="widefat">
							<br /><em>'.__('Enter your SalesAutoPilot API Password. You can get your API Key from the SalesAutoPilot account: Settings >> Integration >> API Keys.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('List ID', 'ulp').':</th>
						<td>
							<input type="text" id="ulp_salesautopilot_list_id" name="ulp_salesautopilot_list_id" value="'.esc_html($popup_options['salesautopilot_list_id']).'" class="ic_input_number">
							<br /><em>'.__('Enter your List ID (Ex. 34567). You can get your API Key from the SalesAutoPilot account.', 'ulp').'</em>
						</td>
					</tr>
					<tr>
						<th>'.__('Form ID', 'ulp').':</th>
						<td>
							<input type="text" id="ulp_salesautopilot_form_id" name="ulp_salesautopilot_form_id" value="'.esc_html($popup_options['salesautopilot_form_id']).'" class="ic_input_number">
							<br /><em>'.__('Enter your Form ID (Ex. 12345). You can get your API Key from the SalesAutoPilot account.', 'ulp').'</em>
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
		if (isset($ulp->postdata["ulp_salesautopilot_enable"])) $popup_options['salesautopilot_enable'] = "on";
		else $popup_options['salesautopilot_enable'] = "off";
		if ($popup_options['salesautopilot_enable'] == 'on') {
			if (empty($popup_options['salesautopilot_username'])) $errors[] = __('Invalid SalesAutoPilot API Username.', 'ulp');
			if (empty($popup_options['salesautopilot_password'])) $errors[] = __('Invalid SalesAutoPilot API Password.', 'ulp');
			if (empty($popup_options['salesautopilot_list_id'])) $errors[] = __('Invalid SalesAutoPilot List ID.', 'ulp');
			if (empty($popup_options['salesautopilot_form_id'])) $errors[] = __('Invalid SalesAutoPilot Form ID.', 'ulp');
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
		if (isset($ulp->postdata["ulp_salesautopilot_enable"])) $popup_options['salesautopilot_enable'] = "on";
		else $popup_options['salesautopilot_enable'] = "off";
		return array_merge($_popup_options, $popup_options);
	}
	function subscribe($_popup_options, $_subscriber) {
		if (empty($_subscriber['{subscription-email}'])) return;
		$popup_options = array_merge($this->default_popup_options, $_popup_options);
		if ($popup_options['salesautopilot_enable'] == 'on') {
			$data = array(
				'email' => $_subscriber['{subscription-email}'],
				'mssys_firstname' => $_subscriber['{subscription-name}'],
				'mssys_lastname' => '',
				'mssys_fullname' => $_subscriber['{subscription-name}']
			);
			if (!empty($_subscriber['{subscription-phone}'])) $data['mssys_phone'] = $_subscriber['{subscription-phone}'];
			$request = json_encode($data);

			$url = 'http://restapi.emesz.com/subscribe/'.urlencode(trim($popup_options['salesautopilot_list_id'])).'/form/'.urlencode(trim($popup_options['salesautopilot_form_id']));
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_USERPWD, $popup_options['salesautopilot_username'].':'.$popup_options['salesautopilot_password']);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
					
			$response = curl_exec($curl);
			curl_close($curl);
		}
	}
}
$ulp_salesautopilot = new ulp_salesautopilot_class();
?>