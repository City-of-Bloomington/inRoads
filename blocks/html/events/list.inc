<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\Result $this->events
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<section>
    <h1><?php echo $this->_(['event', 'events', 2]); ?></h1>
    <div>
    <?php
        if (Person::isAllowed('events', 'add')) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                BASE_URI.'/events/update',
                $this->_('event_add'),
                'add'
            );
        }
    ?>
    </div>
    <table>
    <?php
        foreach ($this->events as $e) {
            $jurisdiction = View::escape($e->getJurisdiction()->getName());

            $url = BASE_URI.'/events/view?event_id='.$e->getId();

            echo "
            <tr><td><a href=\"$url\">$jurisdiction</a></td>
                <td><a href=\"$url\">{$e->getEventType()}</a></td>
                <td><a href=\"$url\">{$e->getSeverity()}</a></td>
                <td><a href=\"$url\">{$e->getStatus()}</a></td>
                <td><a href=\"$url\">{$e->getCreated(DATE_FORMAT)}</a></td>
            </tr>
            ";
        }
    ?>
    </table>
</section>