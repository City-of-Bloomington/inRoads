<?php
/**
 * @copyright 2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Auth;

$fields = ['firstname', 'lastname', 'username', 'email', 'role', 'authentication_method'];
foreach ($fields as $f) {
	$$f = isset($_GET[$f]) ? parent::escape($_GET[$f]) : '';
}
?>
<section>
    <header>
        <h1><?= $this->_(['user', 'users', 10]); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if (Auth::isAllowed('users', 'update')) {
                echo $helper->buttonLink(BASE_URI.'/users/update', $this->_('user_add'), 'add');
            }
        ?>
        </div>
    </header>
    <form method="get">
        <fieldset>
            <?php
                $h = $this->template->getHelper('field');

                foreach (['firstname', 'lastname', 'username', 'email'] as $f) {
                    echo $h->field([
                        'label' => $this->_($f),
                        'name'  => $f,
                        'id'    => $f,
                        'value' => $$f
                    ]);
                }

				global $ZEND_ACL;
				$options = [['value'=>'']];
				foreach (array_reverse($ZEND_ACL->getRoles()) as $r) { $options[] = ['value'=>$r]; }
				echo $h->field([
                    'name'     => 'role',
                    'id'       => 'role',
                    'label'    => $this->_('role'),
                    'value'    => $role,
                    'type'     => 'select',
                    'options'  => $options
				]);


                global $AUTHENTICATION_METHODS;
                $options = [['value'=>'']];
                foreach (array_keys($AUTHENTICATION_METHODS) as $m) { $options[] = ['value'=>$m]; }
                echo $h->field([
                    'name'     => 'authentication_method',
                    'id'       => 'authentication_method',
                    'label'    => $this->_('authenticationMethod'),
                    'value'    => $authentication_method,
                    'type'     => 'select',
                    'options'  => $options
                ]);

                // Preserve any extra parameters passed in
                foreach ($_REQUEST as $key=>$value) {
                    if (!in_array($key, $fields)) {
                        $key   = self::escape($key);
                        $value = self::escape($value);
                        echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
                    }
                }
            ?>
            <button type="submit" class="search"><?= $this->_('search'); ?></button>
        </fieldset>
    </form>
    <?php
        if ($this->users) { $this->_include('users/list.inc'); }
    ?>
</section>
