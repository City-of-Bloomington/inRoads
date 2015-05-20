<?php
/**
 * @copyright 2014-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<section>
    <h1><?php echo View::escape($this->event->getGeography_description()); ?></h1>
    <div class="tools">
    <?php
        if (Person::isAllowed('events', 'edit')) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                BASE_URI.'/events/update?event_id='.$this->event->getId(),
                $this->_('event_edit'),
                'edit'
            );
        }
    ?>
    </div>

    <div id="map"></div>
    <?php
        $this->template->addToAsset('scripts', OPENLAYERS.'/build/ol.js');
        $this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');
    ?>
    <script type="text/javascript">
        PHP.mapdata = '<?php echo $this->event->getGeography(); ?>';
    </script>

    <p><?php echo View::escape($this->event->getDescription()); ?></p>

    <table>
        <tr><th><?php echo $this->_('eventType'); ?></th>
            <td><?php echo $this->event->getEventType(); ?></td>
        </tr>
        <tr><th><?php echo $this->_('startDate'); ?></th>
            <td><?php echo $this->event->getStartDate(DATE_FORMAT); ?></td>
        </tr>
        <tr><th><?php echo $this->_('endDate'); ?></th>
            <td><?php echo $this->event->getEndDate(DATE_FORMAT); ?></td>
        </tr>
    </table>
</section>
