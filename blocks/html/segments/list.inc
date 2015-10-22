<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->segments
 */
?>
<div class="panelList">
<?php
    foreach ($this->segments as $segment) {
        $street    = self::escape($segment->getStreet());
        $from      = self::escape($segment->getStreetFrom());
        $to        = self::escape($segment->getStreetTo());
        $direction = self::escape($segment->getDirection());

        $helper = $this->template->getHelper('buttonLink');
        $editButton = $helper->buttonLink(
            BASE_URI.'/segments/update?segment_id='.$segment->getId(),
            $this->_('segment_edit'),
            'edit'
        );
        $deleteButton = $helper->buttonLink(
            BASE_URI.'/segments/delete?id='.$segment->getId(),
            $this->_('segment_delete'),
            'delete'
        );
        echo "
        <div>
            <dl class=\"inRoads-eventSegment\">
                <dt>{$this->_('street')}</dt>     <dd>$street</dd>
                <dt>{$this->_('direction')}</dt>  <dd>$direction</dd>
                <dt>{$this->_('streetFrom')}</dt> <dd>$from</dd>
                <dt>{$this->_('streetTo')}</dt>   <dd>$to</dd>
            </dl>
            $editButton $deleteButton
        </div>
        ";
    }
?>
</div>
