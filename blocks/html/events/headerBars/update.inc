<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Event $this->event
 */
?>
<h1 class="headerBar-title"><?= $this->template->title; ?></h1>
<div class="headerBar-tools">
<?php
    $helper = $this->template->getHelper('buttonLink');
    echo $helper->buttonLink(
        BASE_URI.'/events/view?id='.$this->event->getId(),
        $this->template->_('cancel'),
        'cancel'
    );
?>
</div>