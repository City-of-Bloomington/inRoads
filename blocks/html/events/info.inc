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
    <h1><?php echo View::escape($this->event->getSummary()); ?></h1>
    <div class="tools">
    <?php
        if (Person::isAllowed('events', 'edit')) {
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(
                BASE_URI.'/events/update?id='.$this->event->getId(),
                $this->_('event_edit'),
                'edit'
            );
        }
    ?>
    </div>

    <?php
        $geography = $this->event->getGeography();
        if ($geography) {
            $this->template->addToAsset('scripts', OPENLAYERS.'/build/ol.js');
            $this->template->addToAsset('scripts', BASE_URI.'/js/mapDisplay.js');
            echo "
            <div id=\"map\"></div>
            <script type=\"text/javascript\">
                PHP.mapdata = '$geography';
            </script>
            ";

        }
    ?>

    <p><?php echo View::escape($this->event->getDescription()); ?></p>

    <?php
        $format = $this->event->isAllDay() ? DATE_FORMAT : DATETIME_FORMAT;
    ?>
    <table>
        <tr><th><?php echo $this->_('startDate'); ?></th>
            <td><?php echo $this->event->getStart($format); ?></td>
        </tr>
        <tr><th><?php echo $this->_('endDate'); ?></th>
            <td><?php echo $this->event->getEnd($format); ?></td>
        </tr>
    </table>
</section>
