<head>
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI; ?>/css/reset.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI; ?>/css/layouts/<?php echo $this->layout; ?>.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI; ?>/css/screen.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI; ?>/css/Font-Awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo OPENLAYERS; ?>/build/ol.css" />
	<title><?php echo isset($this->title) ? self::escape($this->title) : APPLICATION_NAME; ?></title>
	<script type="text/javascript">
        var PHP = {
            BASE_URI: '<?php echo BASE_URI; ?>',
            DEFAULT_LATITUDE:  <?php echo ini_get('date.default_latitude' ); ?>,
            DEFAULT_LONGITUDE: <?php echo ini_get('date.default_longitude'); ?>
        };
	</script>
</head>
