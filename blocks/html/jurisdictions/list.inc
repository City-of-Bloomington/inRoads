<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\Result $this->jurisdictions
 */
use Application\Models\Person;
use Blossom\Classes\View;
use Blossom\Classes\Url;
?>
<section>
    <h1><?php echo $this->_(['jurisdiction', 'jurisdictions', 2]); ?></h1>
    <div>
    <?php
        if (Person::isAllowed('jurisdictions', 'add')) {
            $h = $this->template->getHelper('buttonLink');
            echo $h->buttonLink(
                BASE_URI.'/jurisdictions/update',
                $this->_('jurisdiction_add'),
                'add'
            );
        }
    ?>
    </div>
    <table>
    <?php
        foreach ($this->jurisdictions as $j) {
            $name        = View::escape($j->getName());
            $domain      = View::escape($j->getDomain());
            $email       = View::escape($j->getEmail());
            $phone       = View::escape($j->getPhone());
            $description = View::escape($j->getDescription());

            $url = BASE_URI.'/jurisdictions/view?jurisdiction_id='.$j->getId();
            echo "
            <tr><td><a href=\"$url\">$name</a></td>
                <td><a href=\"$url\">$domain</a></td>
            </tr>
             ";
        }
    ?>
    </table>
</section>
