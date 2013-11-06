<?php
/**
*
* @package testing
* @copyright (c) 2013 Board3 Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @group functional
*/
class phpbb_functional_portal_redirect_test extends \board3\portal\tests\testframework\functional_test_case
{
	public function setUp()
	{
		parent::setUp();
		$this->login();
		$this->admin_login();
		$this->add_lang(array('mods/portal'));
		$this->enable_board3_portal_ext();
	}

	protected function enable_board3_portal_ext()
	{
		$enable_portal = false;
		$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&sid=' . $this->sid);
		$disabled_extensions = $crawler->filter('tr.ext_disabled')->extract(array('_text'));
		foreach ($disabled_extensions as $extension)
		{
			if (strpos($extension, 'Board3 Portal') !== false)
			{
				$enable_portal = true;
			}
		}

		if ($enable_portal)
		{
			$crawler = self::request('GET', 'adm/index.php?i=acp_extensions&mode=main&action=enable_pre&ext_name=board3%2fportal&sid=' . $this->sid);
			$form = $crawler->selectButton('Enable')->form();
			$crawler = self::submit($form);
			$this->assertContains('The extension was enabled successfully', $crawler->text());
		}
	}

	public function test_redirect()
	{
		if (function_exists('apache_get_modules'))
		{
			$modules = apache_get_modules();
			$mod_rewrite = in_array('mod_rewrite', $modules);
		}
		else
		{
			$mod_rewrite =  (getenv('HTTP_MOD_REWRITE')=='On') ? true : false;
		}

		if ($mod_rewrite)
		{
			$crawler = self::request('GET', '');
			$this->assertContains('Board3 Portal', $crawler->text());
		}
	}
}