<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\Url;

class AddressService
{
    private static function jsonRequest($url)
    {
        $response = Url::get($url);
        if ($response) {
            $json = json_decode($response);
            return $json;
        }
    }
	/**
	 * Return an array of street information
	 *
	 * The array must have the streetName as the key.
	 * The value must be the street_id.
	 *
	 * @param string $query
	 * @return array
	 */
	public static function searchStreets($query)
	{
		$results = [];
		if (defined('ADDRESS_SERVICE')) {
			$url = new Url(ADDRESS_SERVICE.'/streets');
			$url->format = 'json';
			$url->streetName = $query;

			$json = self::jsonRequest($url);
            foreach ($json->streets as $street) {
                $results[$street->name] = $street->id;
            }
		}
		return $results;
	}

	/**
	 * Returns an array of streets that intersect the provided street
	 *
	 * The array must have streetName as the key
	 * The value must be the street_id
	 *
	 * @param string $streetName
	 * @return array
	 */
	public static function intersectingStreets($streetName)
	{
        $results = [];
        if (defined('ADDRESS_SERVICE')) {
            $url = new Url(ADDRESS_SERVICE.'/intersections/streets.php');
            $url->format = 'json';
            $url->street = $streetName;

 			$json = self::jsonRequest($url);
 			if (count($json)) {
                foreach ($json->streets as $street) {
                    $results[$street->name] = $street->id;
                }
            }
       }
        return $results;
	}

	public static function intersection($streetName, $otherStreet)
	{
        $results = [];
        if (defined('ADDRESS_SERVICE')) {
            $url = new Url(ADDRESS_SERVICE.'/intersections');
            $url->format = 'json';
            $url->street = $streetName;
            $url->intersectingStreet = $otherStreet;

            $json = self::jsonRequest($url);
            if (count($json)) {
                return $json[0];
            }
        }
	}
}