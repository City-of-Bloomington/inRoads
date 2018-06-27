<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array  $this->notifications
 * @param string $this->type
 */
declare (strict_types=1);
use Application\Auth;
?>
<section>
    <header>
        <h2><?= $this->type; ?></h2>
        <div class="tools">
        <?php
            $userCanEdit = Auth::isAllowed('notifications', 'update');
            if ($userCanEdit) {
                $h = $this->template->getHelper('buttonLink');
                echo $h->buttonLink(
                    BASE_URI.'/notifications/update?type='.$this->type,
                    $this->_('add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table>
    <?php
        $userCanDelete = Auth::isAllowed('notifications', 'delete');
        $editButton    = '';
        $deleteButton  = '';

        foreach ($this->notifications as $n) {
            if ($userCanEdit) {
                $editButton = $h->buttonLink(
                    BASE_URI.'/notifications/update?id='.$n->id,
                    $this->_('edit'),
                    'edit'
                );
            }
            if ($userCanDelete) {
                $deleteButton = $h->buttonLink(
                    BASE_URI.'/notifications/delete?id='.$n->id,
                    $this->_('delete'),
                    'delete'
                );
            }
            $email = parent::escape($n->email);
            echo "
            <tr><td>$editButton</td>
                <td>$email</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
