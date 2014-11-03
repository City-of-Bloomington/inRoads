<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Jurisdiction $this->jurisdiction
 */
use Blossom\Classes\View;

$fields = ['name', 'domain', 'email', 'phone', 'description'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->jurisdiction->$get());
}

$title = $this->jurisdiction->getId() ? $this->_('jurisdiction_edit') : $this->_('jurisdiction_add');
?>
<section>
    <h1><?php echo $title; ?></h1>
    <form method="post" action="">
        <fieldset>
            <input name="jurisdiction_id" type="hidden" value="<?php echo $this->jurisdiction->getId(); ?>" />

            <table>
                <tr><td><label for="name"><?php echo $this->_('name'); ?></label></td>
                    <td><input name="name" id="name" value="<?php echo $name; ?>"
                               required="required" inputmode="latin-name" />
                    </td>
                </tr>
                <tr><td><label for="domain"><?php echo $this->_('domain'); ?></label></td>
                    <td><input name="domain" id="domain" value="<?php echo $domain; ?>"
                               required="required" />
                    </td>
                </tr>
                <tr><td><label for="email"><?php echo $this->_('email'); ?></label></td>
                    <td><input name="email" id="email" value="<?php echo $email; ?>"
                               required="required" type="email"/>
                    </td>
                </tr>
                <tr><td><label for="phone"><?php echo $this->_('phone'); ?></label></td>
                    <td><input name="phone" id="phone" value="<?php echo $phone; ?>"
                               type="tel" />
                    </td>
                </tr>
            </table>
            <div>
                <div><label for="description"><?php echo $this->_('description'); ?></label></div>
                <textarea name="description" id="description"><?php echo $description; ?></textarea>
            </div>
            <?php
                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(BASE_URI.'/jurisdictions/view?jurisdiction_id='.$this->jurisdiction->getId());
            ?>
        </fieldset>
    </form>
</section>