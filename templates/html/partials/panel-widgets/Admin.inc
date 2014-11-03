<nav>
	<h2><?php echo $this->_('admin'); ?></h2>
	<ul>
	<?php
		$li = '<li><a href="%s">%s</a></li>';
		$items = [
            'person'       => 'people',
            'user'         => 'users',
            'jurisdiction' => 'jurisdictions'
        ];
        foreach ($items as $single=>$plural) {
            echo sprintf($li, BASE_URI."/$plural", $this->_([$single, $plural, 2]));
        }
	?>
	</ul>
</nav>
