<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Segment $this->segment
 */
use Blossom\Classes\Url;

$title = $this->segment->getId() ? $this->_('segment_edit') : $this->_('segment_add');
$street = self::escape($this->segment->getStreet());
?>
<section class="editPanel">
    <h1><?= $title; ?></h1>
    <form method="get" action="<?= BASE_URI; ?>/streets/search">
        <fieldset>
            <input name="return_url" type="hidden" value="<?= Url::current_url(); ?>" />

            <dl><dt><label  for="street"><?= $this->_('street'); ?></label></dt>
                <dd><input name="street" id="street" /></dd>
            </dl>
            <button type="submit" class="search"><?= $this->_('search'); ?></button>
        </fieldset>
    </form>
</section>
