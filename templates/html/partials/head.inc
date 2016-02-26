<?php
use Application\Models\EventType;
?>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title><?php echo isset($this->title) ? self::escape($this->title) : APPLICATION_NAME; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
            DEFAULT_LONGITUDE: <?= ini_get('date.default_longitude'); ?>,
            deleteConfirmation: function () {
                return confirm("Are you really sure you want to delete this?\n\nOnce deleted it will be gone forever.");
            }
        };
	</script>
	<style type="text/css">
	<?php
        foreach (EventType::types() as $type) {
            $class = $type->getCode();
            // Convert hex to rgb array
            $color = implode(', ', sscanf($type->getColor(), '%02x%02x%02x'));

            echo "
            .$class      { border-color:rgba($color, 1); }
            .$class > h2 { border-color:rgba($color, 1); }
			.$class > a  { border-color:rgba($color, 1); }
			";
        }
	?>
	</style>
	<meta property="og:url" content="<?= BASE_URL ?>/events" />
	<meta property="og:title" content="<?= APPLICATION_NAME ?>" />
	<meta property="og:description" content="<?= APPLICATION_DESCRIPTION ?>" />
	<meta property="og:image" content="<?= BASE_URL ?>/css/images/inroads-facebook-thumb.png" />

<?php if(defined("APPLICATION_TWITTER_SITE") && !empty(APPLICATION_TWITTER_SITE)): ?>
	<meta property="twitter:card" content="summary_large_image" />
	<meta property="twitter:site" content="<?= APPLICATION_TWITTER_SITE ?>" />
	<meta property="twitter:title" content="<?= APPLICATION_NAME ?>" />
	<meta property="twitter:description" content="<?= APPLICATION_DESCRIPTION ?>" />
	<meta property="twitter:image" content="<?= BASE_URL ?>/css/images/inroads-facebook-thumb.png" />
<?php endif ?>

</head>
