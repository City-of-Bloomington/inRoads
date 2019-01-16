<?php
/**
 * @copyright 2015-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Event $this->event
 * @param array $this->segments
 * @param bool  $this->disableButtons (optional)
 */
use Application\Auth;
?>
<section class="editPanel">
    <header>
    <?php
        $helper = $this->template->getHelper('buttonLink');
        $userCanEdit   = !$this->disableButtons && Auth::isAllowed('segments', 'update');
        $userCanDelete = !$this->disableButtons && Auth::isAllowed('segments', 'delete');

        if ($userCanEdit) {
            echo $helper->buttonLink(
                BASE_URI.'/segments/update?event_id='.$this->event->getId(),
                $this->_('segment_add'),
                'add'
            );
        }
    ?>
    </header>
    <div id="segmentsList">
    <?php
        $editButton   = '';
        $deleteButton = '';

        foreach ($this->segments as $segment) {
            $street    = self::escape($segment->getStreet());
            $from      = self::escape($segment->getStreetFrom());
            $to        = self::escape($segment->getStreetTo());
            $direction = self::escape($segment->getDirection());

            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    BASE_URI.'/segments/update?segment_id='.$segment->getId(),
                    $this->_('segment_edit'),
                    'edit'
                );
            }
            if ($userCanDelete) {
                $deleteButton = $helper->buttonLink(
                    BASE_URI.'/segments/delete?id='.$segment->getId(),
                    $this->_('segment_delete'),
                    'delete'
                );
            }
            echo "
            <div>
                <dl class=\"inRoads-eventSegment\">
                    <dt>{$this->_('street'    )}</dt><dd>$street</dd>
                    <dt>{$this->_('direction' )}</dt><dd>$direction</dd>
                    <dt>{$this->_('streetFrom')}</dt><dd>$from</dd>
                    <dt>{$this->_('streetTo'  )}</dt><dd>$to</dd>
                </dl>
                $editButton $deleteButton
            </div>
            ";
        }
    ?>
    </div>
</section>
