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

            echo "
            <tr><td>$jurisdiction</td>
                <td>{$e->getEventType()}</td>
                <td>{$e->getSeverity()}</td>
                <td>{$e->getStatus()}</td>
                <td>{$e->getCreated(DATE_FORMAT)}</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>