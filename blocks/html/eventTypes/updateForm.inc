<?php
/**
 * @copyright 2015-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param EventType $this->eventType
 */
use Blossom\Classes\View;

foreach (['id', 'code', 'name', 'description', 'color', 'cifsType'] as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->eventType->$get());
}
$isDefault = $this->eventType->isDefault();
$title     = $id ? $this->_('eventType_edit') : $this->_('eventType_add');
?>
<section>
    <h1><?= $title; ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/eventTypes/update">
        <fieldset>
            <input name="eventType_id" type="hidden" value="<?= $id; ?>" />
            <?php
                $helper = $this->template->getHelper('field');
                foreach (['code', 'name', 'color'] as $f) {
                    echo $helper->field(['name'=>$f, 'id'=>$f, 'label'=>$this->_($f), 'value'=>$$f, 'required'=>true]);
                }

                foreach (['cifsType', 'description'] as $f) {
                    echo $helper->field(['name'=>$f, 'id'=>$f, 'label'=>$this->_($f), 'value'=>$$f]);
                }
                echo $helper->field([
                    'name'    => 'isDefault',
                    'id'      => 'isDefault',
                    'label'   => $this->_('isDefault'),
                    'value'   => $isDefault,
                    'type'    => 'select',
                    'options' => [
                        ['value'=>1, 'label'=>$this->_('yes')],
                        ['value'=>0, 'label'=>$this->_('no' )]
                    ]
                ]);
            ?>
            <?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/eventTypes');
            ?>
        </fieldset>
    </form>
</section>
