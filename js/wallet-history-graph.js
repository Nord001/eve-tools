$(document).ready(function() {
	// Set plot options
	var options = {
		lines: { show: true },
		points: { show: true },
		xaxis: {mode: 'time'},
		yaxis: {tickFormatter: function(val, axis) {
				if (val > 1000000000)
					return (val/1000000000).toFixed(axis.tickDecimals) + " B";
				else if (val > 1000000)
					return (val/1000000).toFixed(axis.tickDecimals) + " M";
				else if (val > 1000)
					return (val/1000).toFixed(axis.tickDecimals) + " k";
				else
					return "0";
				}
			},
		legend: {container: $('#legend')}
	};
	
	// Retrieve json object and plot data
	$.getJSON('/php/get-wallet-data.php', function(datasets) {
		var data = [];
		$.each(datasets, function(key, val) {
			data.push(datasets[key]);
		});
		$.plot($('#chart'), data, options);			
	});
});
