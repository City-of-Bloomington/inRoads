<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Jurisdiction $this->jurisdiction
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<section>
    <h1><?php echo View::escape($this->jurisdiction->getName()); ?></h1>
    <div>
    <?php
        if (Person::isAllowed('jurisdictions', 'add')) {
            $h = $this->template->getHelper('buttonLink');
            echo $h->buttonLink(
                BASE_URI.'/jurisdictions/update?jurisdiction_id='.$this->jurisdiction->getId(),
                $this->_('jurisdiction_edit'),
                'edit'
            );
        }
    ?>
    </div>
    <table>
    <?php
    foreach (['name', 'domain', 'email', 'phone'] as $key) {
        $get = 'get'.ucfirst($key);
        $value = View::escape($this->jurisdiction->$get());

        echo "<tr><td>{$this->_($key)}</td><td>$value</td></tr>";
    }
    ?>
    </table>
    <p><?php echo View::escape($this->jurisdiction->getDescription()); ?></p>
</section>