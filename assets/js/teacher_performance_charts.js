// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawPassesChart);
google.setOnLoadCallback(drawO24Chart);
google.setOnLoadCallback(drawU24Chart);
google.setOnLoadCallback(retentionTurnoverChart);

$(window).resize(function(){
	waitForFinalEvent(function(){
	  drawPassesChart();
	  drawO24Chart();
	  drawU24Chart();
	  retentionTurnoverChart();
	  //...
	}, 500, "some unique string");
});

var waitForFinalEvent = (function () {
  var timers = {};
  return function (callback, ms, uniqueId) {
    if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (timers[uniqueId]) {
      clearTimeout (timers[uniqueId]);
    }
    timers[uniqueId] = setTimeout(callback, ms);
  };
})();

function drawPassesChart() {
  if(VSACRM.DataTable.pass.length == 1) {
    $('#pass_chart_div').html("There is no lesson to show.");
    return;
  }

  // Create the data table.
  var data = google.visualization.arrayToDataTable(VSACRM.DataTable.pass);

  // Set chart options
  var options = {
    title: 'Teacher performance - Lessons',
    width: '100%',
    height: 'auto',
    hAxis: {
      textStyle: {
        fontSize: 9
      }
    },
    vAxis: {
      textStyle: {
        fontSize: 11
      },
      format: '#',
      minValue: 0
    },
    colors: ['#336600'],
    pointSize: 4
  };

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.LineChart(document.getElementById('pass_chart_div'));
  chart.draw(data, options);
}

function drawO24Chart() {
  if(VSACRM.DataTable.o24.length == 1) {
    $('#o24_chart_div').html("There is no cancellation over 24 hours to show.");
    return;
  }

  // Create the data table.
  var data = google.visualization.arrayToDataTable(VSACRM.DataTable.o24);

  // tooltips
  data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});

  for(var i = 0; i < data.getNumberOfRows(); i++) {
    var key = data.getValue(i, 0);
    data.setCell(i, 2, setToolTip(VSACRM.o24_count_data[key]));
  }

  // Set chart options
  var options = {
    title: 'Teacher performance - Cancellation over 24 hours',
    width: '100%',
    height: 'auto',
    hAxis: {
      textStyle: {
        fontSize: 9
      }
    },
    vAxis: {
      textStyle: {
        fontSize: 11
      },
      format: '#',
      minValue: 0
    },
    tooltip: {isHtml: true},
    colors: ['#cc3333'],
    pointSize: 4
  };

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.LineChart(document.getElementById('o24_chart_div'));
  chart.draw(data, options);
}

function drawU24Chart() {
  if(VSACRM.DataTable.u24.length == 1) {
    $('#u24_chart_div').html("There is no cancellation under 24 hours to show.");
    return;
  }

  // Create the data table.
  var data = google.visualization.arrayToDataTable(VSACRM.DataTable.u24);

  // tooltips
  data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});

  for(var i = 0; i < data.getNumberOfRows(); i++) {
    var key = data.getValue(i, 0);
    data.setCell(i, 2, setToolTip(VSACRM.u24_count_data[key]));
  }

  // Set chart options
  var options = {
    title: 'Teacher performance - Cancellation under 24 hours',
    width: '100%',
    height: 'auto',
    hAxis: {
      textStyle: {
        fontSize: 9
      }
    },
    vAxis: {
      textStyle: {
        fontSize: 11
      },
      format: '#',
      minValue: 0
    },
    tooltip: {isHtml: true},
    pointSize: 4
  };

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.LineChart(document.getElementById('u24_chart_div'));
  chart.draw(data, options);
}

function retentionTurnoverChart() {
  if(VSACRM.DataTable.retention_turnover.retention == 0 &&
    VSACRM.DataTable.retention_turnover.turnover == 0) {
    $('#retention_turnover').html("There is no retention and turnover data to show.");
    return;
  }

  // Create the data table.
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Retention - Turnover');
  data.addColumn('number', 'Rate');
  data.addRows([
    ['Retention', VSACRM.DataTable.retention_turnover.retention],
    ['Turnover', VSACRM.DataTable.retention_turnover.turnover]
  ]);

  // Set chart options
  var options = {
    title: 'Retention - Turnover rate',
    width: '100%',
    height: 'auto',
  };

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.PieChart(document.getElementById('retention_turnover'));
  chart.draw(data, options);
}

function setToolTip(count_data) {
  var reasons = VSACRM.cancellation_reasons;
  var html = '<small><table class="table table-striped">';
  var total = 0;
  for(var key in count_data) {
    html += '<tr><td>' + ucwords(reasons[key].replace('_', ' ')) + '</td><td>' + count_data[key] + '</td></tr>';
    total += count_data[key];
  }
  html += '<tr><td><strong>Total</strong></td><td><strong>' + total + '</strong></td></tr>'
  html += '</table></small>';
  return html;
}

function ucwords(str) {
  return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    return letter.toUpperCase();
  });
}
