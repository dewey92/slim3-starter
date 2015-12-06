<?php

namespace Katanium\Extensions;

/**
* My Twig Extension
*/
class MyTwigExtension extends \Twig_Extension
{
	/**
	 * Name of this extension.
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'myExtension';
	}

	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('cut_str', array($this, 'cut_str')),
		);
	}

	/**
	 * blabla
	 */
	public function cut_str($str, $start, $end)
	{
		return substr($str, $start, $end) . '...';
	}
}