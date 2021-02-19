<?php

/**
 * Copyable Captha plugin
 * Copyright (C) 2016 artoodetoo
 * Special thanks to Visman for his help
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

class addon_copyable_captcha extends flux_addon
{
	var $lang;
	var $styles;
	var $spans;

	function register($manager)
	{
		global $pun_user;

		if (!$pun_user['is_guest']) return;

		$manager->bind('register_after_validation', array($this, 'hook_register_after_validation'));
		$manager->bind('register_before_header', array($this, 'hook_register_before_header'));
		$manager->bind('register_before_submit', array($this, 'hook_register_before_submit'));
	}

	function load_lang()
	{
		global $pun_user;

		if (isset($this->lang)) return;

		$user_lang = file_exists(PUN_ROOT.'lang/'.$pun_user['language'].'/copyable_captcha.php')
			? $pun_user['language']
			: 'English';
		require PUN_ROOT.'lang/'.$user_lang.'/copyable_captcha.php';

		$this->lang = $lang_copyable_captcha;
	}

	function hook_register_after_validation()
	{
		global $errors, $cookie_name, $cookie_seed;

		if (isset($_POST['req_word']) && isset($_COOKIE[$cookie_name.'_captcha']) && substr_count($_COOKIE[$cookie_name.'_captcha'], '-') === 1) {
			list($hash, $time) = explode('-', $_COOKIE[$cookie_name.'_captcha']);
			$word = $_POST['req_word'];
			if ((int)$time <= time() - 120 || $hash !== sha1(strtolower($word).$cookie_seed.'secret'.$time)) {
				$this->load_lang();
				$errors[] = $this->lang['Captcha error'];
			}
		} else {
			$this->load_lang();
			$errors[] = $this->lang['Captcha error'];
		}
	}


	function hook_register_before_header()
	{
		global $required_fields, $errors, $cookie_name, $cookie_seed;

		$this->load_lang();
		$required_fields['req_word'] = $this->lang['Captcha'];

		$time = time();
		$word = random_pass(mt_rand(4, 6));
		$hash = sha1(strtolower($word).$cookie_seed.'secret'.$time);
		forum_setcookie($cookie_name.'_captcha', $hash.'-'.$time, $time + 120);

		$array = str_split($word);
		$mixin = random_pass(mt_rand(1, 3));
		$i = -1;
		$this->styles = '';
		foreach (str_split($mixin) as $ch) {
			$i = mt_rand($i+1, count($array));
			array_splice($array, $i, 0, $ch);
			$this->styles .= '.masq i:nth-child('.($i + 1).'){display:none;} ';
		}
		$this->spans = '<i>'.implode('</i><i>', $array).'</i>';
	}


	function hook_register_before_submit()
	{
		global $lang_common;

		$this->load_lang();

?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $this->lang['Captcha legend'] ?></legend>
					<div class="infldset">
						<style> .masq i {font-style:normal;} <?php echo $this->styles ?></style>
						<p><?php echo sprintf($this->lang['Captcha info'], $this->spans) ?></p>
						<label class="required"><strong><?php echo $this->lang['Captcha'] ?> <span><?php echo $lang_common['Required'] ?></span></strong><br /><input type="text" name="req_word" size="25" maxlength="25" /><br /></label>
					</div>
				</fieldset>
			</div>
<?php

	}
}
