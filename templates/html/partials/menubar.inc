<nav>
	<?php
		$a = '<a href="%s">%s</a>';
        $items = [
            'event'        => 'events',
            'person'       => 'people',
            'user'         => 'users',
            'jurisdiction' => 'jurisdictions'
        ];
        foreach ($items as $single=>$plural) {
            echo sprintf($a, BASE_URI."/$plural", $this->_([$single, $plural, 2]));
        }
	?>
</nav>
