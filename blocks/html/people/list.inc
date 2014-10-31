<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $people
 */
use Application\Models\Person;
use Blossom\Classes\View;
use Blossom\Classes\Url;
?>
<section>
	<h1><?php echo $this->translate(['person','people',2]); ?></h1>
	<div>
	<?php
        $return_url = Url::current_url();
        $helper = $this->template->getHelper('buttonLink');

        if (Person::isAllowed('people')) {
            echo $helper->buttonLink(
                BASE_URI."/people/update?return_url=$return_url",
                $this->translate('person_add'),
                'add'
            );
        }
	?>
	</div>
	<table>
		<thead>
			<tr><th><?php echo $this->_('username'); ?></th>
				<th><?php echo $this->_('name'); ?></th>
				<th><?php echo $this->_(['email','emails',1]); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->people as $person) {
				$name = View::escape($person->getFullname());
				$url = $person->getUri();
				echo "
				<tr><td><a href=\"$url\">{$person->getUsername()}</a></td>
					<td><a href=\"$url\">$name</a></td>
					<td><a href=\"$url\">{$person->getEmail()}</a></td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</section>
