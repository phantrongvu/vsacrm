/**
 * Created with JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 12/01/13
 * Time: 10:49 PM
 * To change this template use File | Settings | File Templates.
 */

$(function() {
	
	var defView = 'agendaWeek';
	if ( $(window).width() < 640 ){
		defView = VSACRM.current_calendar_view || 'agendaDay'
	}
	else
	{
		defView = VSACRM.current_calendar_view || 'agendaWeek';
	}
  $('#calendar').fullCalendar({
    editable: true,
    disableResizing: true,
    disableDragging: false,
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay'
    },
    titleFormat: {
      month: 'MMMM yyyy',                             // September 2009
      week: "d MMM [ yyyy]{ '&#8212;' d MMM yyyy}", // 7 Sep - 13 Sep 2009
      day: 'dddd, d MMM, yyyy'                  // Tuesday, 8 Sep, 2009
    },
    columnFormat: {
      month: 'ddd',    // Mon
      week: 'ddd d/M', // Mon 7/9
      day: 'dddd d/M'  // Monday 7/9
    },
    defaultView: defView,
    allDaySlot: false,
    firstDay: 1,
    minTime: VSACRM.studio_start_time,
    maxTime: VSACRM.studio_end_time,
    events: {
      url: VSACRM.base_url + 'calendar/json',
      type: 'GET',
      data: {
        timezoneOffset: (new Date()).getTimezoneOffset(),
        studio_id: VSACRM.studio_id,
        teacher_id: VSACRM.teacher_id
      },
      error: function() {
        alert('Server is busy, please refresh the browser to continue...');
      }
    },
    viewDisplay: function(view) {		
      if(view.name == 'agendaWeek' || view.name == 'agendaDay') {
        // enable drag drop
        view.calendar.options.disableDragging = false;
      } else {
        // disable drag drop
        view.calendar.options.disableDragging = true;
      }
    },
    eventRender: function(event, element) {
      element.qtip({
        content: event.description,
        position: {
          corner: {
            target: 'topMiddle',
            tooltip: 'bottomMiddle'
          }
        },
        style: {
          padding: 4,
          background: '#FFFFFF',
          color: '#000000',
          border: {
            width: 1,
            radius: 5,
            color: '#6699CC'
          },
          tip: 'bottomMiddle'
        }
      });

      // only set cancel if schedule is pending
      // or passed
      if(event.schedule.status == VSACRM.schedule_statuses.STATUS_PENDING ||
        event.schedule.status == VSACRM.schedule_statuses.STATUS_PAID_PENDING ||
        event.schedule.status == VSACRM.schedule_statuses.STATUS_PASSED ||
        event.schedule.status == VSACRM.schedule_statuses.STATUS_UNPAID_PASSED ||
		event.schedule.status == VSACRM.schedule_statuses.STATUS_PAID_GREEN ||
		event.schedule.status == VSACRM.schedule_statuses.STATUS_CANCELED_OVER_24 ||
		event.schedule.status == VSACRM.schedule_statuses.STATUS_CANCELED_IN_24) {
        element.bind('click', function() {
          $('.event-action-modal').data('schedule', event.schedule).modal('show');
        });
      }
    },
    eventDrop: function(event, dayDelta, minuteDelta, allDay, revertFunc) {
      $('.fc-event').qtip('hide');
      $('#dialog-form').data('event', event);
      $('#dialog-form').dialog( "open" );
    },
    loading: function(isLoading, view) {
      if(isLoading) {
        $.blockUI();
      } else {
        $.unblockUI();
      }
    },
	windowResize: function(view) {
		if ( $(window).width() < 640 ){
			$('#calendar').fullCalendar( 'changeView', 'agendaDay' );
		}
		else
		{
			$('#calendar').fullCalendar( 'changeView', 'agendaWeek' );
		}
	}
  });

  if(VSACRM.current_calendar_start) {
    $('#calendar').fullCalendar('gotoDate', new Date(parseInt(VSACRM.current_calendar_start) * 1000));
  }

  // time
  $('#rebook_time_from').change(function() {
    var from = parseFloat($(this).val());
    var to = parseFloat($('#rebook_time_to').val());
    $('#rebook_time_to').val(from + 0.5);
  });

  // date picker
  $('#rebook_date').datepicker({
    dateFormat: 'dd/mm/yy',
    minDate: 0
  });

  // modal dialog
  $('.event-action-modal').on('show', function() {
    // handling current calendar view
    var view = $('#calendar').fullCalendar('getView');
    $('.current-calendar-view').val(view.name);
    $('.current-calendar-start').val(view.start.getTime() / 1000);

    var current_calendar_start = view.start.getTime() / 1000;

    var form = $(this).find('.event-action-form'),
      schedule = $(this).data('schedule');
    var cid = schedule.cid;
    var start = new Date(schedule.start * 1000);
    var note_history = schedule.note_history;

    // update student details
    var student = schedule.event.student;
    var student_str = student.first_name + ' ' + student.last_name;
    $('.event-action-form span.student-name').html(student_str);
    $('.event-action-form span.student-mail').html(student.mail);
    $('.event-action-form span.student-phone').html(student.phone);
    $('.event-action-form span.student-mobile').html(student.mobile);

    // update extra actions
    var extra_actions_str = '';
    var event_edit_url = VSACRM.base_url + 'calendar/event/' + schedule.event.eid + '_' + schedule.cid;
    extra_actions_str += ' <a href="' + event_edit_url + '" class="btn btn-mini">Edit</a>'

    if(typeof schedule.unpaid != 'undefined' && schedule.unpaid && VSACRM.view == 'admin') {
      var mark_paid_one_url = VSACRM.base_url + 'calendar/schedule/mark-paid-one/' + schedule.cid + '?current_calendar_view=' + view.name + '&current_calendar_start=' + current_calendar_start ;
      extra_actions_str += ' <a href="' + mark_paid_one_url + '" class="btn btn-mini">Pay One Lesson</a>';
    }

    if(typeof schedule.unpaid != 'undefined' && schedule.unpaid && VSACRM.view == 'admin') {
      var mark_paid_url = VSACRM.base_url + 'calendar/schedule/mark-paid/' + schedule.event.eid + '?current_calendar_view=' + view.name + '&current_calendar_start=' + current_calendar_start ;
      extra_actions_str += ' <a href="' + mark_paid_url + '" class="btn btn-mini">Pay All Lessons</a>';
    }

    if(typeof schedule.paid != 'undefined' && schedule.paid && VSACRM.view == 'admin') {
      var mark_unpaid_one_url = VSACRM.base_url + 'calendar/schedule/mark-unpaid-one/' + schedule.cid + '?current_calendar_view=' + view.name + '&current_calendar_start=' + current_calendar_start ;
      extra_actions_str += ' <a href="' + mark_unpaid_one_url + '" class="btn btn-mini">Mark One Unpaid</a>';
    }

    if(typeof schedule.paid != 'undefined' && schedule.paid && VSACRM.view == 'admin') {
      var mark_unpaid_url = VSACRM.base_url + 'calendar/schedule/mark-unpaid/' + schedule.event.eid + '?current_calendar_view=' + view.name + '&current_calendar_start=' + current_calendar_start ;
      extra_actions_str += ' <a href="' + mark_unpaid_url + '" class="btn btn-mini">Mark All Unpaid</a>';
    }



    $('.event-action-form span.extra-actions').html(extra_actions_str);

    // update note history
    var student_note_url = VSACRM.base_url + 'note/search/' + student.sid;
    var extra_actions_str = ' <a href="' + student_note_url + '" class="btn btn-mini">Note history</a>';
    var add_note_url = VSACRM.base_url + 'note/manage?sid=' + student.sid + '&destination=calendar' + '&current_calendar_view=' + view.name + '&current_calendar_start=' + current_calendar_start;
    extra_actions_str += ' <a href="' + add_note_url + '" class="btn btn-mini">Add Notes</a>';
    var note_history_str = extra_actions_str;
    for(var i = 0; i < note_history.length; i++) {
      note_history_str += '<li><strong>' + note_history[i].title + '</strong>: ' + note_history[i].body + '</li>';
    }
    $('.event-action-form div.note-history').html('<ul>' + note_history_str + '</ul>');

    // handling rebook option
    // rebook or not?
    if(schedule.rebook) {
      var start_date = new Date(schedule.start * 1000);
      start_date.setDate(start_date.getDate() + 7);

      var hour = start_date.getHours();
      if(start_date.getMinutes() == 30) {
        hour += 0.5;
      }

      $('#rebook_time_from').val(hour);
      $('#rebook_time_from').trigger('change');
      $('#rebook_date').datepicker('setDate', start_date);
      var min_date = new Date();
      min_date.setDate(start_date.getDate() - 7);
      $('#rebook_date').datepicker('option', 'minDate', min_date);
      $('#product').val(schedule.event.product_id);

      $('.title-rebook').css('display', '');
      $('.title-rebook').css('display', '');
    } else {
      $('.title-rebook').css('display', 'none');
      $('.title-rebook').css('display', 'none');
    }

    // activate the first panel of the accordion
    $( '#accordion' ).accordion( 'option', 'active', 0 );
    $('#ui-accordion-accordion-header-0').trigger('click');

    // auto calculate scheduled_date when cancel over 24 hours
    var calculated_cancel_over_24 = new Date(schedule.calculated_cancel_over_24_time * 1000)
    var hour = calculated_cancel_over_24.getHours();
    if(calculated_cancel_over_24.getMinutes() == 30) {
      hour += 0.5;
    }
    $('#scheduled_date').val($.fullCalendar.formatDate(calculated_cancel_over_24, 'dd/MM/yyyy'));
    $('#scheduled_time_from').val(hour);
    $('#scheduled_time_from').trigger('change');

    // if teacher is viewing, only allow rebook when event has passed
	
	/*console.log( 'SCHEDULE STATUS: '+schedule.status );
	console.log( 'STATUS_CANCELED_IN_24 '+VSACRM.schedule_statuses.STATUS_CANCELED_IN_24 );
	console.log( 'STATUS_CANCELED_OVER_24 '+VSACRM.schedule_statuses.STATUS_CANCELED_OVER_24 );
	console.log( '---------------------------' );*/
	if ( schedule.status == VSACRM.schedule_statuses.STATUS_CANCELED_IN_24 || schedule.status == VSACRM.schedule_statuses.STATUS_CANCELED_OVER_24 )
	{
        $('#ui-accordion-accordion-header-0').hide();
        $('#ui-accordion-accordion-header-1').hide();
        $('#ui-accordion-accordion-header-2').hide();
        $('#ui-accordion-accordion-header-3').hide();
        $('.modal-footer input[type="submit"]').hide();
	}
	else
	{
        $('#ui-accordion-accordion-header-0').show();
        $('#ui-accordion-accordion-header-1').show();
        $('#ui-accordion-accordion-header-2').show();
        $('#ui-accordion-accordion-header-3').show();
        $('.modal-footer input[type="submit"]').show();
		
		if(VSACRM.view == 'teacher') {
		  if(schedule.status == VSACRM.schedule_statuses.STATUS_PASSED || schedule.status == VSACRM.schedule_statuses.STATUS_UNPAID_PASSED) {
			$('#ui-accordion-accordion-header-0').hide();
			$('#ui-accordion-accordion-header-1').hide();
		  } else {
			$('#ui-accordion-accordion-header-0').show();
			$('#ui-accordion-accordion-header-1').show();
		  }
		}
	}

    form.attr('action', VSACRM.base_url + 'calendar/schedule/status/' + cid);
  }).modal({ backdrop: true, show: false });

  // modal dialog
  $( '#dialog-form' ).dialog({
    autoOpen: false,
    height: 250,
    width: 350,
    modal: true,
    buttons: {
      "Reschedule": function() {
        var form = $('#dialog-form');
        var event = form.data('event');
        var mail = $('#dialog-form .send-email-notification').is(':checked') ? 1 : 0;
        var whole_event = $('#dialog-form .apply-whole-event').is(':checked') ? 1 : 0;

        // handling current calendar view
        var view = $('#calendar').fullCalendar('getView');
        var start = parseInt((new Date(event.start)).getTime()) / 1000;
        var end = parseInt((new Date(event.end)).getTime()) / 1000;
        window.location = VSACRM.base_url + 'calendar/schedule/reschedule/' + event.schedule.cid +
          '?start=' + start + '&end=' + end + '&mail=' + mail + '&whole_event=' + whole_event +
          '&current_calendar_view=' + view.name +
          '&current_calendar_start=' + (view.start.getTime() / 1000);
      },
      "Cancel reschedule": function() {
        var form = $('#dialog-form');
        $('#calendar').fullCalendar( 'refetchEvents' );
        $( this ).dialog( "close" );
      }
    },
    close: function() {
      var form = $('#dialog-form');
      $('#calendar').fullCalendar( 'refetchEvents' );
      $( this ).dialog( "close" );
    }
  });

  // accordion
  $('#accordion').accordion({
    autoHeight: false,
    collapsible: true,
    activate: function( event, ui) {
      switch(ui.newHeader.text()){
        case 'Cancel over 24 hours':
          $('.event-action-modal .modal-footer .btn-primary').removeAttr('disabled');
          $('.rad-over-24').attr('checked', 'checked');
          break;

        case 'Cancel under 24 hours':
          $('.event-action-modal .modal-footer .btn-primary').removeAttr('disabled');
          $('.rad-within-24').attr('checked', 'checked');
          break;

        case 'Delete whole event':
          $('.event-action-modal .modal-footer .btn-primary').removeAttr('disabled');
          $('.rad-delete-event').attr('checked', 'checked');
          break;

        case 'Rebook':
          $('.event-action-modal .modal-footer .btn-primary').removeAttr('disabled');
          $('.rad-rebook').attr('checked', 'checked');
          break;

        default:
          $('.event-action-modal .modal-footer .btn-primary').attr('disabled', 'disabled');
          break;
      }
    }
  });

  $('.btn-email-admin').click(function(e) {
    e.preventDefault();

    var view = $('#calendar').fullCalendar('getView');
    $('.email-current-view').val(view.name);
    $('.email-current-start').val(view.start.getTime() / 1000);
    $('.dialog-email-admin').modal('show');
  });
});
