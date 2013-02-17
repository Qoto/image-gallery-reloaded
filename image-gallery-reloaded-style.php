<?php
$options = get_option('igr_options');
?>
<style type="text/css" media="screen" rel="stylesheet">
	#galleria
	{
		height:<?php echo $options['total_height']; ?>px;
	}
	.galleria-container 
	{
		background:#<?php echo $options['color_background']; ?>;
	}
	.galleria-stage
	{
		left: <?php echo $options['gallery_border_width']; ?>px;
		right: <?php echo $options['gallery_border_width']; ?>px;
		top: <?php echo $options['gallery_border_top_height']; ?>px;
		bottom: <?php echo $options['gallery_border_bottom_height']; ?>px;
	}
	.galleria-thumbnails-container
	{
		left:<?php echo $options['gallery_border_width']; ?>px;
		right:<?php echo $options['gallery_border_width']; ?>px;
	}
	.galleria-thumbnails .galleria-image
	{
		border: 1px solid #<?php echo $options['color_thumb_border']; ?>;
		height:<?php echo $options['thumb_height']; ?>px;
		width:<?php echo $options['thumb_width']; ?>px;
	}
	.galleria-info-link
	{
		background-color: #<?php echo $options['color_infolink_background']; ?>; 
	}
	.galleria-info-text
	{
		background-color: #<?php echo $options['color_infolink_background']; ?>;
	}
	.galleria-lightbox-shadow
	{
		background:#<?php echo $options['color_lightbox_border']; ?>;
	}
	
	.galleria-lightbox-content
	{
		background-color:#<?php echo $options['color_lightbox_background']; ?>;
		left:<?php echo $options['lightbox_border_width']; ?>px;
		right:<?php echo $options['lightbox_border_width']; ?>px;
		top:<?php echo $options['lightbox_border_width']; ?>px;
		bottom:<?php echo $options['lightbox_border_width']; ?>px;
	}
	.galleria-thumb-nav-right
	{
		background-color:#<?php echo $options['color_carusel_arrows_background']; ?>;
	}
	.galleria-thumb-nav-left
	{
		background-color:#<?php echo $options['color_carusel_arrows_background']; ?>;
	}
	.galleria-lightbox-image
	{
		left:0;
		right:0;
		bottom:30px;
		top:0;
	}
	<?php if ($options['igr_thumb_event_type'] == 'mouseover' ) : ?>
		.galleria-carousel .galleria-thumb-nav-left, .galleria-carousel .galleria-thumb-nav-right
		{
			display:none;
		}
		.galleria-carousel .galleria-thumbnails-list {
			margin-left: <?php echo $options['gallery_border_width']; ?>px;
			margin-right: <?php echo $options['gallery_border_width']; ?>px;
		}
	<?php endif; ?>
	<?php if (!empty($options['custom_css']) ) : ?>
		<?php echo $options['custom_css']; ?>
	<?php endif; ?>
</style>