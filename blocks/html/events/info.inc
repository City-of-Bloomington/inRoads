<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Blossom\Classes\View;
?>
<section>
    <h1><?php echo View::escape($this->event->getHeadline()); ?></h1>
    <table>
        <tr><th><?php echo $this->_('jurisdiction'); ?></th>
            <td><?php echo View::escape($this->event->getJurisdiction()->getName()); ?></td>
        </tr>
        <tr><th><?php echo $this->_('eventType'); ?></th>
            <td><?php echo $this->event->getEventType(); ?></td>
        </tr>
        <tr><th><?php echo $this->_('severity'); ?></th>
            <td><?php echo $this->event->getSeverity(); ?></td>
        </tr>
        <tr><th><?php echo $this->_('status'); ?></th>
            <td><?php echo $this->event->getStatus(); ?></td>
        </tr>
        <tr><th><?php echo $this->_('created'); ?></th>
            <td><?php echo $this->event->getCreated(DATE_FORMAT); ?></td>
        </tr>
        <tr><th><?php echo $this->_('updated'); ?></th>
            <td><?php echo $this->event->getUpdated(DATE_FORMAT); ?></td>
        </tr>
    </table>
    <p><?php echo View::escape($this->event->getDescription()); ?></p>
    <p><?php echo View::escape($this->event->getDetour()); ?></p>


    <div id="map"></div>
    <?php
        $this->template->addToAsset('scripts', GOOGLE_MAPS);
        $this->tempalte->addToAsset('scripts', BASE_URI.'/js/map.js');
    ?>
</section>