<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Segment $this->segment
 */
use Blossom\Classes\Url;
$this->template->addToAsset('scripts', BASE_URI.'/js/segments/chooseStreet.js');
?>
<section class="editPanel">
    <h1><?= $this->_('segment_add'); ?></h1>
    <ol>
        <li>Start by typing the name of the road impacted by the event. Choose the correct road from the window that pops up.</li>
        <li>Next, we will ask you where the closing begins, where it ends, and in what directions will be closed.</li>
        <li>Repeat to add as many segments as you need.</li>
    </ol>
    <form method="get" action="<?= BASE_URI; ?>/streets/search">
        <fieldset>
            <input name="return_url" type="hidden" value="<?= Url::current_url(); ?>" />

            <dl><dt><label  for="street"><?= $this->_('street'); ?></label></dt>
                <dd><input name="street" id="street" /></dd>
            </dl>
            <button type="submit" class="search" id="findStreetButton">
                <?= $this->_('search'); ?>
            </button>
        </fieldset>
    </form>
</section>
