<?php
use Blossom\Classes\View;
?>
<form method="post" action="<?php echo BASE_URI; ?>/login/login">
	<fieldset><legend><?php echo $this->_('login'); ?></legend>
		<input name="return_url" type="hidden" value="<?php echo View::escape($this->return_url); ?>" />
		<dl><dt><label for="username"><?php echo $this->_('username'); ?></label></dt>
            <dd><input name="username" id="username" /></dd>
        </dl>
        <dl><dt><label for="password"><?php echo $this->_('password'); ?></label></dt>
            <dd><input type="password" name="password" id="password" /></dd>
        </dl>
		<button type="submit" class="login"><?php echo $this->_('login'); ?></button>
	</fieldset>
</form>
