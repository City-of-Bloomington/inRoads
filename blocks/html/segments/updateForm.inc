<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Segment $this->segment
 */
use Application\Models\AddressService;
use Application\Models\Segment;

$title = $this->segment->getId() ? $this->_('segment_edit') : $this->_('segment_add');

$fields = [
    'street', 'streetFrom', 'streetTo', 'direction',
    'startLatitude', 'startLongitude', 'endLatitude', 'endLongitude'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->segment->$get());
}
?>
<section class="editPanel">
    <h1><?= $title; ?></h1>
    <form method="post" action="<?= BASE_URI; ?>/segments/update">
        <fieldset>
            <input name="event_id"   type="hidden" value="<?= $this->segment->getEvent_id(); ?>" />
            <input name="segment_id" type="hidden" value="<?= $this->segment->getId(); ?>" />

            <dl><dt><label  for="street"><?= $this->_('street'); ?></label></dt>
                <dd><?php
                        $type = $street ? 'hidden' : 'text';
                        echo "<input name=\"street\" id=\"street\" type=\"$type\" value=\"$street\" />";
                        if ($type==='hidden') { echo $street; }
                    ?>
                </dd>
            </dl>
            <?php
                $intersections = AddressService::intersectingStreets($this->segment->getStreet());
                foreach (['streetFrom'=>$this->segment->getStreetFrom(), 'streetTo'=>$this->segment->getStreetTo()] as $field=>$value) {
                    $options = '<option value=""></option>';

                    foreach ($intersections as $name=>$id) {
                        $n = self::escape($name);
                        $selected = $value===$name ? 'selected="true"' : '';
                        $options.= "<option $selected>$n</option>";
                    }

                    echo "
                    <dl><dt><label for=\"$field\">{$this->_($field)}</label></dt>
                        <dd><select name=\"$field\" id=\"$field\">
                                $options
                            </select>
                        </dd>
                    </dl>
                    ";
                }
            ?>

            <dl><dt><label   for="direction"><?= $this->_('direction'); ?></label></dt>
                <dd><select name="direction" id="direction"><option value=""></option>
                    <?php
                        foreach (Segment::$directions as $dir) {
                            $selected = $direction===$dir ? 'selected="true"' : '';
                            echo "<option $selected>$dir</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>

            <button type="submit" class="save"><?= $this->_('save'); ?></button>
        </fieldset>
    </form>
</section>
