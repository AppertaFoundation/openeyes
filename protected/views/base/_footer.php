	<div id="footer">
		<h6>&copy; Copyright OpenEyes Foundation 2011 &nbsp;&nbsp;|<!--&nbsp;&nbsp; Terms of Use &nbsp;&nbsp;|&nbsp;&nbsp; Legals &nbsp;&nbsp;|-->&nbsp;&nbsp; <a href="#" id="support-info-link">served, with love, by <?php echo trim(`hostname`)?></a></h6>
	</div> <!-- #footer -->

<script type="text/javascript">
$('#support-info-link').unbind('click').click(function() {
	$.fancybox({
		'padding'				: 0,
		'href'					: '/site/debuginfo',
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
});
</script>
