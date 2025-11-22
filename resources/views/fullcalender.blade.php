<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laravel Fullcalender Tutorial</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body>
  <div class="container">
    <h1>Laravel FullCalender Tutorial</h1>
    <div id="calendar"></div>
  </div>

  <script>
    $(document).ready(function() {
      let SITEURL = "{{ url('/') }}"
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      })

      let calendar = $('#calendar').fullCalendar({
        editable: true,
        events: SITEURL + "/fullcalender",
        displayEventTime: true,
        editable: true,
        eventRender: function(event, element, view) {
          if (event.allDay === 'true') {
            event.allDay = true;
          } else {
            event.allDay = false;
          }
        },
        selectable: true,
        selectHelper: true,
        select: function(start, end, allDay) {
          let title = prompt('Event Title:');
          if (title) {
            let formattedStart = $.fullCalendar.formatDate(start, "Y-MM-DD")
            let formattedEnd = $.fullCalendar.formatDate(end, "Y-MM-DD")
            $.ajax({
              url: SITEURL + "/fullcalenderAjax",
              data: {
                title: title,
                start: formattedStart,
                end: formattedEnd,
                type: 'add',
              },
              type: "POST",
              success: function(data) {
                displayMessage("Event Successfully Created")
                calendar.fullCalendar('renderEvent', {
                  id: data.id,
                  title: title,
                  start: start,
                  end: end,
                  allDay: allDay
                }, true);
                calendar.fullCalendar('unselect');
              }
            });
          }
        },
        eventDrop: function(event, delta) {
          let start = $.fullCalendar.formatDate(event.start, "Y-MM-DD")
          let end = $.fullCalendar.formatDate(event.end, "Y-MM-DD")
          $.ajax({
            url: SITEURL + '/fullcalenderAjax',
            data: {
              title: event.title,
              start: start,
              end: end,
              id: event.id,
              type: 'update'
            },
            type: "POST",
            success: function(response) {
              displayMessage("Event Successfully Updated");
            }
          });
        },
        eventClick: function(event) {
          let deleteMessage = confirm("Do you want to delete this event ?")
          if (deleteMessage) {
            $.ajax({
              type: "POST",
              url: SITEURL + '/fullcalenderAjax',
              data: {
                id: event.id,
                type: 'delete'
              },
              success: function(response) {
                calendar.fullCalendar('removeEvents', event.id)
                displayMessage("Event Successfully Deleted");
              }
            });
          }
        }
      });
    });

    function displayMessage(message) {
      toastr.success(message, 'Event');
    }
  </script>
</body>

</html>
