<h2>Test letter printing</h2>
<a href="#" id="print_test">PRINT STUFF</a>

<script type="text/javascript">
$('#print_test').click(function() {
	printUrl('/waitingList/printletters?operations[]=1&operations[]=2');
});
</script>