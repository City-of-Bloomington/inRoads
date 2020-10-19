<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->results
 */
use Application\Url;

$url = !empty($_REQUEST['return_url'])
    ? new Url($_REQUEST['return_url'])
    : new Url(BASE_URL.'/segments/view');

echo "
<section>
    <h1>{$this->_(['street', 'streets', 2])}</h1>
    <ul>
";
    foreach ($this->results as $streetName=>$street_id) {
        $url->street = $streetName;
        $n = self::escape($streetName);

        $href = !empty($_REQUEST['callback'])
            ? "javascript:self.opener.$_REQUEST[callback]('$streetName', $street_id)"
            : $url;

        echo "<li><a href=\"$href\">$n</a></li>";
    }
echo "
    </ul>
</section>
";
