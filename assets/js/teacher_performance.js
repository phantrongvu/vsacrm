(function($){
  var formatDate = function(date, format) {
    var m = date.getMonth();
    var d = date.getDate();
    var y = date.getFullYear();
    var wn = date.getWeekNumber();
    var w = date.getDay();
    var s = {};
    var hr = date.getHours();
    var pm = (hr >= 12);
    var ir = (pm) ? (hr - 12) : hr;
    var dy = date.getDayOfYear();
    if (ir == 0) {
      ir = 12;
    }
    var min = date.getMinutes();
    var sec = date.getSeconds();
    var parts = format.split(''), part;
    for ( var i = 0; i < parts.length; i++ ) {
      part = parts[i];
      switch (parts[i]) {
        case 'a':
          part = date.getDayName();
          break;
        case 'A':
          part = date.getDayName(true);
          break;
        case 'b':
          part = date.getMonthName();
          break;
        case 'B':
          part = date.getMonthName(true);
          break;
        case 'C':
          part = 1 + Math.floor(y / 100);
          break;
        case 'd':
          part = (d < 10) ? ("0" + d) : d;
          break;
        case 'e':
          part = d;
          break;
        case 'H':
          part = (hr < 10) ? ("0" + hr) : hr;
          break;
        case 'I':
          part = (ir < 10) ? ("0" + ir) : ir;
          break;
        case 'j':
          part = (dy < 100) ? ((dy < 10) ? ("00" + dy) : ("0" + dy)) : dy;
          break;
        case 'k':
          part = hr;
          break;
        case 'l':
          part = ir;
          break;
        case 'm':
          part = (m < 9) ? ("0" + (1+m)) : (1+m);
          break;
        case 'M':
          part = (min < 10) ? ("0" + min) : min;
          break;
        case 'p':
        case 'P':
          part = pm ? "PM" : "AM";
          break;
        case 's':
          part = Math.floor(date.getTime() / 1000);
          break;
        case 'S':
          part = (sec < 10) ? ("0" + sec) : sec;
          break;
        case 'u':
          part = w + 1;
          break;
        case 'w':
          part = w;
          break;
        case 'y':
          part = ('' + y).substr(2, 2);
          break;
        case 'Y':
          part = y;
          break;
      }
      parts[i] = part;
    }
    return parts.join('');
  }

  var postBack = function() {
    var _date = $('#widgetCalendar').DatePickerGetDate();

    if(formatDate(_date[0], 'd-m-y') == formatDate(_date[1], 'd-m-y')) {
      return;
    }

    var date = [_date[0].getTime() / 1000, _date[1].getTime() / 1000];
    var uid = $('#teacher_id').val();
    var view = VSACRM.view;

    if(!uid) {
      alert('Please select a teacher');
      return;
    }

    var queryString = 'start=' + date[0] + '&end=' + date[1] + '&view=' + view + '&uid=' + uid;
    window.location = VSACRM.base_url + 'report/teacher_performance?' + queryString;
  }
  $.extend(VSACRM, {postBack: postBack});

  $('#teacher_id').change(function() {
    VSACRM.postBack();
  });

  $('.btn-weekly').click(function(e) {
    e.preventDefault();
    VSACRM.view = 'week';
    VSACRM.postBack();
  });

  $('.btn-monthly').click(function(e) {
    e.preventDefault();
    VSACRM.view = 'month';
    VSACRM.postBack();
  });

  var initLayout = function() {
    $('#widgetCalendar').DatePicker({
      flat: true,
      format: 'd B, Y',
      date: [new Date(VSACRM.start * 1000), new Date(VSACRM.end * 1000)],
      calendars: 3,
      mode: 'range',
      starts: 1,
      onChange: function(formated) {
        VSACRM.postBack();
      }
    });

    var state = false;
    $('#widgetField>a').bind('click', function(){
      $('#widgetCalendar').stop().animate({height: state ? 0 : $('#widgetCalendar div.datepicker').get(0).offsetHeight}, 500);
      state = !state;
      return false;
    });
    $('#widgetCalendar div.datepicker').css('position', 'absolute');

    // set default date
    var defaultDate = $('#widgetCalendar').DatePickerGetDate();
    defaultDate[0] = formatDate(defaultDate[0], 'd B, Y');
    defaultDate[1] = formatDate(defaultDate[1], 'd B, Y');
    $('#widgetField span').get(0).innerHTML = defaultDate.join(' &divide; ');
  }

  EYE.register(initLayout, 'init');
})(jQuery)
