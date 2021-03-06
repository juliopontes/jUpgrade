<?php
/**
 * jUpgrade
 *
 * @version		$Id: 
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguirre. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * jUpgrade class for K2 migration
 *
 * This class migrates the K2 extension
 *
 * @since		1.1.0
 */
class jUpgradeComponentK2 extends jUpgradeExtensions
{
	/**
	 * Check if extension migration is supported.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	protected function detectExtension()
	{
		return true;
	}

	/**
	 * Migrate tables
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	public function migrateExtensionCustom()
	{

		// Fixing access
		$query = "UPDATE #__k2_items SET access = '1' WHERE access = '0'";
		$this->db_new->setQuery($query);
		$this->db_new->query();

		// Check for query error.
		$error = $this->db_new->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// Fixing access
		$query = "UPDATE #__k2_categories SET access = '1' WHERE access = '0'";
		$this->db_new->setQuery($query);
		$this->db_new->query();

		// Check for query error.
		$error = $this->db_new->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		return true;
	}
}
