<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<script>
	function subst() {
		var vars={};
		var x=window.location.search.substring(1).split('&');
		for (var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
		var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
		for (var i in x) {
			var y = document.getElementsByClassName(x[i]);
			for (var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
		}
	}
	</script>
	<body style="margin: 0; padding: 0; margin-bottom: 61.6em;" onload="subst()">
		<div style="width: 48%; float: left">
			{{FOOTER_LEFT}}
		</div>
		<div style="width: 37%; float: left; margin-left: auto; margin-right: auto;">
			{{FOOTER_MIDDLE}}
		</div>
		<div style="width: 15%; float: right">
			{{FOOTER_RIGHT}}
		</div>
	</body>
</html>
