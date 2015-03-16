/**
 * jquery.flot.dashes.js
 *
 * This code is mostly copied from the "drawSeriesLines" function in the main jquery.flot.js library.
 * Important note: The change is to use setLineDash() to enabled dashed lines, which only works in chrome.
 */

(function(){

	function init(plot) {

		plot.hooks.processDatapoints.push(function (plot, series, datapoints) {

			if (!series.dashes.show) return;

			plot.hooks.draw.push(function (plot, ctx) {

				var plotOffset = plot.getPlotOffset();

				function plotDashes(datapoints, xoffset, yoffset, axisx, axisy) {

					var points = datapoints.points,
						ps = datapoints.pointsize,
						prevx = null,
						prevy = null;

					ctx.beginPath();
					for (var i = ps; i < points.length; i += ps) {
						var x1 = points[i - ps],
							y1 = points[i - ps + 1],
							x2 = points[i],
							y2 = points[i + 1];

						if (x1 == null || x2 == null)
							continue;

						// clip with ymin
						if (y1 <= y2 && y1 < axisy.min) {
							if (y2 < axisy.min)
								continue; // line segment is outside
							// compute new intersection point
							x1 = (axisy.min - y1) / (y2 - y1) * (x2 - x1) + x1;
							y1 = axisy.min;
						} else if (y2 <= y1 && y2 < axisy.min) {
							if (y1 < axisy.min)
								continue;
							x2 = (axisy.min - y1) / (y2 - y1) * (x2 - x1) + x1;
							y2 = axisy.min;
						}

						// clip with ymax
						if (y1 >= y2 && y1 > axisy.max) {
							if (y2 > axisy.max)
								continue;
							x1 = (axisy.max - y1) / (y2 - y1) * (x2 - x1) + x1;
							y1 = axisy.max;
						} else if (y2 >= y1 && y2 > axisy.max) {
							if (y1 > axisy.max)
								continue;
							x2 = (axisy.max - y1) / (y2 - y1) * (x2 - x1) + x1;
							y2 = axisy.max;
						}

						// clip with xmin
						if (x1 <= x2 && x1 < axisx.min) {
							if (x2 < axisx.min)
								continue;
							y1 = (axisx.min - x1) / (x2 - x1) * (y2 - y1) + y1;
							x1 = axisx.min;
						} else if (x2 <= x1 && x2 < axisx.min) {
							if (x1 < axisx.min)
								continue;
							y2 = (axisx.min - x1) / (x2 - x1) * (y2 - y1) + y1;
							x2 = axisx.min;
						}

						// clip with xmax
						if (x1 >= x2 && x1 > axisx.max) {
							if (x2 > axisx.max)
								continue;
							y1 = (axisx.max - x1) / (x2 - x1) * (y2 - y1) + y1;
							x1 = axisx.max;
						} else if (x2 >= x1 && x2 > axisx.max) {
							if (x1 > axisx.max)
								continue;
							y2 = (axisx.max - x1) / (x2 - x1) * (y2 - y1) + y1;
							x2 = axisx.max;
						}

						if (x1 != prevx || y1 != prevy)
							ctx.moveTo(axisx.p2c(x1) + xoffset, axisy.p2c(y1) + yoffset);

						prevx = x2;
						prevy = y2;
						ctx.lineTo(axisx.p2c(x2) + xoffset, axisy.p2c(y2) + yoffset);
					}
					ctx.stroke();
				}

				ctx.save();
				ctx.translate(plotOffset.left, plotOffset.top);
				ctx.lineJoin = "round";
				ctx.setLineDash(series.dashes.style);

				var lw = series.lines.lineWidth,
					sw = series.shadowSize;

				if (lw > 0 && sw > 0) {
					// draw shadow as a thick and thin line with transparency
					ctx.lineWidth = sw;
					ctx.strokeStyle = "rgba(0,0,0,0.1)";
					// position shadow at angle from the mid of line
					var angle = Math.PI / 18;
					plotDashes(series.datapoints, Math.sin(angle) * (lw / 2 + sw / 2), Math.cos(angle) * (lw / 2 + sw / 2), series.xaxis, series.yaxis);
					ctx.lineWidth = sw / 2;
					plotDashes(series.datapoints, Math.sin(angle) * (lw / 2 + sw / 4), Math.cos(angle) * (lw / 2 + sw / 4), series.xaxis, series.yaxis);
				}

				ctx.lineWidth = lw;
				ctx.strokeStyle = series.color;

				if (lw > 0)
					plotDashes(series.datapoints, 0, 0, series.xaxis, series.yaxis);

				ctx.restore();
				ctx.setLineDash([0]); // reset line dash
			});

		});
	}

	$.plot.plugins.push({
		init: init,
		options: {
			series: {
				dashes: {
					show: false,
					lineWidth: 2,
					style: [4, 4]
				}
			}
		},
		name: 'dashes',
		version: '0.1'
	});
}());