<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Contao;


/**
 * Class ModuleLogout
 *
 * Front end module "logout".
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Core
 */
class ModuleLogout extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate;


	/**
	 * Logout the current user and redirect
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['logout'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set last page visited
		if ($this->redirectBack)
		{
			$_SESSION['LAST_PAGE_VISITED'] = $this->getReferer();
		}

		$this->import('FrontendUser', 'User');

		$blnUseJumpTo = ($this->jumpTo > 0);
		$strRedirect = \Environment::get('base');

		// Redirect to last page visited
		if ($this->redirectBack && !empty($_SESSION['LAST_PAGE_VISITED']))
		{
			$objLastPage = \PageModel::findByIdOrAlias($this->getPageIdFromUrl($_SESSION['LAST_PAGE_VISITED']));

			// Check whether the page is protected (see #6210)
			if ($objLastPage !== null && !$objLastPage->protected)
			{
				$blnUseJumpTo = false;
				$strRedirect = $_SESSION['LAST_PAGE_VISITED'];
			}
		}

		// Redirect to the jumpTo page
		if ($blnUseJumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) !== null)
		{
			$strRedirect = $this->generateFrontendUrl($objTarget->row());
		}

		// Log out and redirect
		if ($this->User->logout())
		{
			$this->redirect($strRedirect);
		}

		return '';
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		return;
	}
}
