<?php namespace Dish;

class CLI {

	/**
	 * Process CLI parameters
	 *
	 * @param   array  $values
	 * @param   array  $defaults
	 * @return  array
	 */

	public static function parameters($values, $defaults = [])
	{
		$params = $defaults;
		$params['cmds'] = [];

		for($i = 0; $i < count($values); $i++)
		{
			$param = str_replace('--', '', $values[$i]);

			if(isset($params[$param]))
			{
				$value = $values[$i+1];

				if(preg_match('/^\-\-/', $value) <= 0)
				{
					$params[$param] = $value;
					$i++;
				}
				else
				{
					$params[$param] = true;
				}
			}
			else
			{
				$params['cmds'][] = $_SERVER['argv'][$i];
			}
		}

		return $params;
	}

    /**
     * Print output to terminal.
     *
     * @param   string  $string
     * @param   bool    $sameline
     * @return  void
     */

    public static function write($string, $sameline = false)
    {
        if($sameline == true)
		{
            $string = "\r".$string;
        }
		else
		{
            $string .= "\n";
        }

        if(defined('STDOUT'))
        {
            fwrite(STDOUT, $string);
        }
    }

    /**
     * Print error to terminal.
     *
     * @param   string  $string
     * @return  void
     */

    public static function error($string)
    {
		static::write("\033[91m" . $string . "\033[0m");
    }

	/**
     * Print good to terminal.
     *
     * @param   string  $string
     * @return  void
     */

    public static function good($string)
    {
		static::write("\033[92m" . $string . "\033[0m");
    }

	/**
	 * Print warn to terminal.
	 *
	 * @param   string  $string
	 * @return  void
	 */

	public static function warn($string)
	{
		static::write("\033[93m" . $string . "\033[0m");
	}

	/**
	 * Start a color
	 *
	 * @param   string  $string
	 * @return  void
	 */

	public static function color($type = 'end')
	{
		$colors = [
			'error'		=> "\033[91m",
			'good'		=> "\033[92m",
			'warn'		=> "\033[93m",
			'magenta'	=> "\033[95m",
			'cyan'		=> "\033[96m",
			'plain'		=> "\033[0m",
			'grey'		=> "\033[90m",
			'end'		=> "\033[0m",
		];

		return $colors[$type];
	}

    /**
     * Print blank lines to terminal.
     *
     * @param   int     $num
     * @return  void
     */

    public static function newline($num = 1)
    {
        for($i = 0; $i < $num; $i++)
		{
            static::write('');
        }
    }

    /**
     * Ask a question
     *
     * @param    string
     * @return    mixed
     */

    public static function ask($str)
    {
        self::warn($str);
        $handle = fopen ("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        self::newline();

        return trim($line);
    }

    /**
     * Confirm (Y/N)
     *
     * @param    string
     * @return   bool
     */

    public static function confirm($str)
    {
        self::warn($str);
        $handle = fopen ("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if(trim(strtolower($line)) == 'y')
        {
            self::newline();
            return true;
        }
        else
        {
            return false;
        }
    }

}
