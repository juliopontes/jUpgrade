<?php
/**
 * jUpgrade
 *
 * @version		$Id$
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

/**
 * Upgrade class for categories
 *
 * This class takes the categories from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class jUpgradeCategories extends jUpgradeCategory
{
	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.5
	 */
	protected $source = '#__sections';

	/**
	 * @var		string	The name of the destination database table.
	 * @since	0.4.5
	 */
	protected $destination = '#__categories';

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function setDestinationData()
	{
		// Content categories
		$this->section = 'com_content'; 

		// Get the source data.
		$rows	= $this->getSourceData();

		// Truncate jupgrade_categories table
		$clean	= $this->cleanDestinationData('jupgrade_categories');

		// Insert uncategorized id
		$query = "INSERT INTO `jupgrade_categories` (`old`, `new`) VALUES (0, 2)";
		$this->db_new->setQuery($query);
		$this->db_new->query();

		// Check for query error.
		$error = $this->db_new->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		//
		// Insert the categories
		//
		foreach ($rows as $row)
		{
			// Inserting the category
			$this->insertCategory($row);

			 // Childen categories
			$query = "SELECT `id` AS sid, `title`, `alias`, `section` AS extension, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`"
			." FROM {$this->config_old['prefix']}categories"
			." WHERE section = {$row['sid']}"
			." ORDER BY id ASC";
			$this->db_old->setQuery($query);
			$categories = $this->db_old->loadAssocList();

			for($y=0;$y<count($categories);$y++){

				// Correct some values
				$categories[$y]['params'] = $this->convertParams($categories[$y]['params']);
				$categories[$y]['title'] = str_replace("'", "&#39;", $categories[$y]['title']);
				$categories[$y]['description'] = str_replace("'", "&#39;", $categories[$y]['description']);
				$categories[$y]['access'] = $categories[$y]['access']+1;
				$categories[$y]['language'] = '*';

				// Correct alias
				if ($categories[$y]['alias'] == "") {
					$categories[$y]['alias'] = JFilterOutput::stringURLSafe($categories[$y]['title']);
				}

				// Inserting category and asset
				$this->insertCategory($categories[$y], $row['title']);
			}

		}
	}
}
