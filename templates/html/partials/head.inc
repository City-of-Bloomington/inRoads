<?php
use Application\Models\EventType;
?>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo isset($this->title) ? self::escape($this->title) : APPLICATION_NAME; ?></title>
	<link rel="stylesheet" type="text/css" href="<?= BASE_URI; ?>/css/screen.css" />
	<link rel="stylesheet" type="text/css" href="<?= OPENLAYERS; ?>/ol.css" />
	<?php
        if ($this->theme && is_file($this->theme.'/css/screen.css')) {
            $uri = BASE_URI.'/css/themes/'.THEME.'/css/screen.css';
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$uri\" />";
        }
	?>
	<script type="text/javascript">
        var PHP = {
            BASE_URI: '<?= BASE_URI; ?>',
            DEFAULT_LATITUDE:  <?= ini_get('date.default_latitude' ); ?>,
            DEFAULT_LONGITUDE: <?= ini_get('date.default_longitude'); ?>
        };
	</script>
	<style type="text/css">
	<?php
        foreach (EventType::types() as $type) {
            $class = $type->getCode();
            $color = implode(', ', $type->getColor());

            echo "
            .$class      { border-color:rgba($color, .6); }
            .$class > h2 { border-color:rgba($color, .6); }
			.$class > a  { border-color:rgba($color, .6); }
			";
        }
	?>
	</style>
</head>
