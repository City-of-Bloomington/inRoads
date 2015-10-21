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

			$json = json_decode(Url::get($url));
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
            $url = new Url(ADDRESS_SERVICE.'/intersections');
            $url->format = 'json';
            $url->street = $streetName;
            
 			$json = json_decode(Url::get($url));
			foreach ($json->streets as $street) {
                $results[$street->name] = $street->id;
			}
       }
        return $results;

	}
}