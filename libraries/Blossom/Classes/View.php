<?php
/**
 * @copyright 2006-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

abstract class View
{
	protected $vars = array();

	abstract public function render();

	/**
	 * Configures the gettext translations
	 */
	public function __construct(array $vars=null)
	{
		if (count($vars)) {
			foreach ($vars as $name=>$value) {
				$this->vars[$name] = $value;
			}
		}

        $locale = LOCALE.'.utf8';

        putenv("LC_ALL=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain('labels',   APPLICATION_HOME.'/language');
        bindtextdomain('messages', APPLICATION_HOME.'/language');
        textdomain('labels');
	}

	/**
	 * Magic Method for setting object properties
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function __set($key,$value) {
		$this->vars[$key] = $value;
	}
	/**
	 * Magic method for getting object properties
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		if (isset($this->vars[$key])) {
			return $this->vars[$key];
		}
		return null;
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key) {
		return array_key_exists($key,$this->vars);
	}

	/**
	 * Cleans strings for output
	 *
	 * There are more bad characters than htmlspecialchars deals with.  We just want
	 * to add in some other characters to clean.  While here, we might as well
	 * have it trim out the whitespace too.
	 *
	 * @param array|string $string
	 * @param CONSTANT $quotes Optional, the desired constant to use for the htmlspecidalchars call
	 * @return string
	 */
	public static function escape($input,$quotes=ENT_QUOTES)
	{
		if (is_array($input)) {
			foreach ($input as $key=>$value) {
				$input[$key] = self::escape($value,$quotes);
			}
		}
		else {
			$input = htmlspecialchars(trim($input), $quotes, 'UTF-8');
		}

		return $input;
	}

    /**
     * Returns the gettext translation of msgid
     *
     * The default domain is "labels".  Any other text domains must be passed
     * in the second parameter.
     *
     * For entries in the PO that are plurals, you must pass msgid as an array
     * $this->translate( ['msgid', 'msgid_plural', $num] )
     *
     * @param mixed $msgid String or Array
     * @param string $domain Alternate domain
     * @return string
     */
    public function translate($msgid, $domain=null)
    {
        if (is_array($msgid)) {
            return $domain
                ? dngettext($domain, $msgid[0], $msgid[1], $msgid[2])
                : ngettext (         $msgid[0], $msgid[1], $msgid[2]);
        }
        else {
            return $domain
                ? dgettext($domain, $msgid)
                : gettext (         $msgid);
        }
    }

    /**
     * Alias of $this->translate()
     */
    public function _($msgid, $domain=null)
    {
        return $this->translate($msgid, $domain);
    }

    /**
     * Converts the PHP date format string syntax into something for humans
     *
     * @param string $format
     * @return string
     */
    public static function translateDateString($format)
    {
        return str_replace(
            ['m',  'n' , 'd',  'j',  'Y',    'H',  'g',  'i',  's',  'a'],
            ['mm', 'mm', 'dd', 'dd', 'yyyy', 'hh', 'hh', 'mm', 'ss', 'am'],
            $format
        );
    }

    /**
     * Converts a PHP date format string to jQuery's date format syntax
     */
    public static function jQueryDateFormat($format)
    {
        return str_replace(
            ['m',  'n', 'd',  'j', 'Y' ],
            ['mm', 'm', 'dd', 'd', 'yy'],
            $format
        );
    }
}
