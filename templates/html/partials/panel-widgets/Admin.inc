<nav>
	<h2><?php echo $this->_('admin'); ?></h2>
	<ul>
	<?php
		$li = '<li><a href="%s">%s</a></li>';
		echo sprintf($li, BASE_URI.'/people', $this->_(['person','people',2]));
		echo sprintf($li, BASE_URI.'/users' , $this->_(['user'  ,'users' ,2]));
	?>
	</ul>
</nav>
