<?php
/**
 * Original version
 * Axel < axel[at]quelloffen.com >
 * http://www.joomlaconsulting.de
 *
 * @package   BackendToken
 *
 * @author    RolandD Cyber Produkis <contact@rolandd.com>
 * @copyright Copyright (C) 2019 RolandD Cyber Produksi. All rights reserved.
 * @license   GNU/GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://rolandd.com
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

/**
 * Backend Token.
 *
 * @package     BackendToken
 * @since       2.0.0
 */
class plgSystemBackendtoken extends CMSPlugin
{
	/**
	 * An application instance
	 *
	 * @var    AdministratorApplication
	 * @since  2.0.0
	 */
	protected $app;

	/**
	 * Trigger on after being initialised.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 *
	 * @throws  Exception
	 */
	public function onAfterInitialise(): void
	{
		if (!$this->app->isClient('administrator'))
		{
			return;
		}

		// Already logged in
		$user = Factory::getUser();

		if (!$user->guest)
		{
			return;
		}

		// Consider request empty
		$request = '';

		// Handle a direct entry to the admin login page
		if ($this->app->input->getMethod() === 'GET')
		{
			$request = $this->app->input->getString('token', 'no token set');
		}

		// Handle after login form submission
		if ($this->app->input->getMethod() === 'POST')
		{
			// Need to get the token from the referer URL
			$uri = $this->app->input->server->getString('HTTP_REFERER');
			$request = $uri->getVar('token', 'no token set');
		}

		$token = $this->params->get('token', 1);

		// Invalid access token
		if ($token !== $request)
		{
			$url = (string) $this->params->get('url');

			// Fallback to site
			if ($url === '')
			{
				$url = Uri::root();
			}

			$this->app->redirect($url);
			$this->app->close();
			die;
		}
	}
}
