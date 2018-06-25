<form method="post" action="<?= BASE_URI; ?>/login/login">
	<fieldset><legend><?= $this->_('login'); ?></legend>
		<input name="return_url" type="hidden" value="<?= parent::escape($this->return_url); ?>" />
		<dl><dt><label for="username"><?= $this->_('username'); ?></label></dt>
            <dd><input name="username" id="username" /></dd>
        </dl>
        <dl><dt><label for="password"><?= $this->_('password'); ?></label></dt>
            <dd><input type="password" name="password" id="password" /></dd>
        </dl>
		<button type="submit" class="login"><?= $this->_('login'); ?></button>
	</fieldset>
</form>
