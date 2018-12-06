<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace numeric\aquila\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	/* @var \phpbb\controller\helper */
	protected $helper;
	protected $template;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper	$helper		Controller helper object
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\template\template $template)
	{
		$this->helper = $helper;
		$this->template = $template;
	}

	/**
	*
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.gen_sort_selects_after' => 'sort_selects',
		);
	}

	public function sort_selects($event)
	{
		$sorts = $event['sorts'];

		foreach ($sorts as $name => $sort_ary)
		{
			$key = $sort_ary['key'];
			$selected = ${$sort_ary['key']};

			// Check if the key is selectable. If not, we reset to the default or first key found.
			// This ensures the values are always valid. We also set $sort_dir/sort_key/etc. to the
			// correct value, else the protection is void. ;)
			if (!isset($sort_ary['options'][$selected]))
			{
				if ($sort_ary['default'] !== false)
				{
					$selected = ${$key} = $sort_ary['default'];
				}
				else
				{
					@reset($sort_ary['options']);
					$selected = ${$key} = key($sort_ary['options']);
				}
			}

			$this->template->assign_vars(array(
				'S_' . strtoupper($key) . '_NAME'	=> $name,
			));

			foreach ($sort_ary['options'] as $option => $text)
			{
				$this->template->assign_block_vars($key, array(
					'VALUE'		=> $option,
					'SELECTED'	=> ($selected == $option) ? ' selected="selected"' : '',
					'TEXT'		=> $text,
				));
			}
		}
	}
}
