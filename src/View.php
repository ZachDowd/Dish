<?php namespace Dish;

class View {

	/**
	 * Content
	 *
	 * @var string
	 */

	protected $content;

	/**
	 * Data
	 *
	 * @var array
	 */

	protected $data = [];

	/**
	 * Inner HTML
	 *
	 * @var string
	 */

	protected $innerHTML;

	/**
	 * Tag name
	 *
	 * @var string
	 */

	protected $tag;

	/**
	 * Views
	 *
	 * @var array
	 */

	protected $views = [];

	/**
	 * parseAttributes
	 *
	 * Parse the attributes string and set the data for the view
	 *
	 * @param	$value string
	 * @return	void
	 */

	public function parseAttributes($str)
	{
		if($str)
		{
			preg_match_all('/(([a-zA-Z0-9\-]+)\=\"([^\"]+))/s', $str, $matches);

			foreach($matches[0] as $i => $value)
			{
				$key = str_replace('-', '_', $matches[2][$i]);
				$this->data[$key] = $matches[3][$i];
			}
		}
	}

	/**
	 * parseSetAttributes
	 *
	 * Parse the attributes from within the <Set /> tag
	 *
	 * @param	$value string
	 * @param	$value string
	 * @return	void
	 */

	public function parseSetAttributes($str = '', $content = '')
	{
		preg_match_all('/(([a-zA-Z0-9\-]+)\=\"([^\"]+))/s', $str, $matches);
		$pair = [];

		foreach($matches[2] as $i => $value)
		{
			$pair[$value] = $matches[3][$i];
		}

		if(isset($pair['value']))
		{
			$this->data[$pair['name']] = $pair['value'];
		}
		else
		{
			$this->data[$pair['name']] = $content;
		}
	}

	/**
	 * parseInnerHTML
	 *
	 * Parse the data in the inner HTML
	 *
	 * @param	$value string
	 * @return	void
	 */

	public function parseInnerHTML($str)
	{
		if($str)
		{
			preg_match_all("/\<Set ([^\>]*?)(\/\>|\>(.*?)\<\/\\Set\>)/s", $str, $matches);

			foreach($matches[0] as $i => $value)
			{
				$this->parseSetAttributes($matches[1][$i], $matches[3][$i]);

				// Remove the <Set /> from the string
				$str = str_replace($matches[0][$i], '', $str);
			}

			$this->data['html'] = $str;
		}
	}

	/**
	 * parse
	 *
	 * Parse the HTML and build views
	 *
	 * @param	$value string
	 * @return	string
	 */

	public function parse($str)
	{
		preg_match_all("/\<([A-Z][^ \n\/\>]+)([^\>]*?)(\/\>|\/?\>((.*?)\<\/\\1\>))/s", $str, $matches);

		foreach($matches[0] as $i => $value)
		{
			$view = new View;
			$view->setTag(trim($matches[1][$i]));
			$view->parseAttributes($matches[2][$i]);
			$view->parseInnerHTML($matches[5][$i]);

			$html = $view->render();
			$html = $view->parse($html);

			$str = str_replace($matches[0][$i], $html, $str);
		}

		return $str;
	}

	/**
	 * parseReplacements
	 *
	 * Parse the replacement variables from the config
	 *
	 * @param	$str string
	 * @param	$replacements array
	 * @return	string
	 */

	public function parseReplacements($str, $replacements)
	{
		foreach($replacements as $key => $value)
		{
			$str = str_replace($key, $value, $str);
		}

		return $str;
	}

	/**
	 * render
	 *
	 * Render the file
	 *
	 * @return	string
	 */

	public function render()
	{
		// Restart the buffer
		ob_start();

		extract($this->data);

		$globals = Config::get('globals');

		foreach($globals as $key => $value)
		{
			$$key = $value;
		}

		unset($globals);

		@include(Config::get('paths.components') . '/' . implode('/', $this->tag) . '.html');

		// Collect file
		$fetched = ob_get_clean();

		// Restart the buffer
		ob_start();

		return $fetched;
	}

	/**
	 * setTag
	 *
	 * Set the tag name
	 *
	 * @param	$value string
	 * @return	void
	 */

	public function setTag($value)
	{
		$values = explode(':', $value);

		if(count($values) > 1)
		{
			$this->tag = $values;
		}
		else
		{
			$this->tag = [$value, $value];
		}
	}
}
