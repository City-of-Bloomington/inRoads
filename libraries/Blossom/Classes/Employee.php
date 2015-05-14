<?php
/**
 * A class for working with entries from a Directory webservice
 *
 * This class is written specifically for the City of Bloomington's
 * Directory web application.
 *
 * @copyright 2011-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

class Employee implements ExternalIdentity
{
	private static $connection;
	private $config;
	private $entry;

	/**
	 * @param array $config
	 * @param string $username
	 * @param string $password
	 * @throws Exception
	 */
	public static function authenticate($username, $password)
	{
        return false;
	}


	/**
	 * Loads an entry from the LDAP server for the given user
	 *
	 * @param array $config
	 * @param string $username
	 */
	public function __construct($username)
	{
		global $DIRECTORY_CONFIG;
		$this->config = $DIRECTORY_CONFIG['Employee'];

		$response = Url::get($this->config['DIRECTORY_SERVER'].'/people/view?format=json;username='.$username);
		if ($response) {
            $this->entry = json_decode($response);
            if (!$this->entry) {
                throw new \Exception('Employee/unknownUser');
            }
		}
		else {
            throw new \Exception('Employee/unknownUser');
		}
	}

	/**
	 * @return string
	 */
	public function getUsername()	{ return $this->entry->username;  }
	public function getFirstname()	{ return $this->entry->firstname; }
	public function getLastname()	{ return $this->entry->lastname;  }
	public function getEmail()		{ return $this->entry->email;     }
	public function getPhone()		{ return $this->entry->office;    }
	public function getAddress()	{ return $this->entry->address;   }
	public function getCity()		{ return $this->entry->city;      }
	public function getState()		{ return $this->entry->state;     }
	public function getZip()		{ return $this->entry->zip;       }
}
