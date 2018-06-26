<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->fullname
 * @param string $this->email
 * @param string $this->phone
 * @param bool   $this->notify_updates
 * @param bool   $this->notify_emergency
 */
declare (strict_types=1);
use Application\Auth;
?>
<section>
    <header>
        <h1><?= $this->fullname; ?></h1>
        <h2><?= $this->username; ?></h2>
        <div class="tools">
        <?php
            if (Auth::isAllowed('account', 'update')) {
                $h = $this->template->getHelper('buttonLink');
                echo $h->buttonLink(
                    BASE_URI.'/account/update',
                    $this->_('edit'),
                    'edit'
                );
            }
        ?>
        </div>
    </header>
    <table>
        <tr><th><?= $this->_('email'); ?></th><td><?= $this->email; ?></th></tr>
        <tr><th><?= $this->_('phone'); ?></th><td><?= $this->phone; ?></th></tr>
        <tr><th><?= $this->_('notify_updates'  ); ?></th>
            <td><?= $this->notify_updates ? $this->_('yes') : $this->_('no');   ?></td>
        </tr>
        <tr><th><?= $this->_('notify_emergency'); ?></th>
            <td><?= $this->notify_emergency ? $this->_('yes') : $this->_('no'); ?></td>
        </tr>
    </table>
</section>
