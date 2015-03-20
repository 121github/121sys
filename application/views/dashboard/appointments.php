
<div id="wrapper">
  <div id="sidebar-wrapper">
 <?php  $this->view('dashboard/navigation.php',$page) ?>
  </div>
  <div id="page-content-wrapper">
    <div id="page-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Appointments</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      <!-- /.row -->
     <div class="row">
    <div class="col-lg-12 col-md-12">
       <div id="appointment-table">
          </div>
       </div>
        
    </div>

</div>
</div>
</div>
<script>
$(document).ready(function(){
appointment.init();
});

var modal = {
default_buttons: function () {
        $('#modal').find('.modal-footer .btn').remove();
        $('#modal').find('.modal-footer').append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
        $('#modal').find('.modal-footer').append('<button class="btn btn-primary confirm-modal" type="button">Confirm</button>');
    },
    clear_buttons: function () {
        $('#modal').find('.modal-footer .btn').remove();
    },
	show_appointment:function(id){
        $('.modal-title').text('appointment #' +id);
          $.ajax({
            url: helper.baseUrl + 'appointments/appointment_modal',
            type: "POST",
            dataType: "JSON",
            data: {id:id}
        }).done(function (response) {
			if(response.success){
			var modal_html="";
			modal_html += "<p>Appointment was set for <b>"+response.data.appointment.date_formatted+"</b></p>";
			modal_html += "<p><ul>";
			modal_html += "<li><b>Title:</b> "+response.data.appointment.title+"</li>"
			modal_html += "<li><b>Notes:</b> "+response.data.appointment.text+"</li>"
			modal_html += "</ul></p>";
			$('#modal').find('.modal-body').html(modal_html);
			modal.clear_buttons();
			$('#modal').find('.modal-footer').append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
			$('#modal').find('.modal-footer').append('<a class="btn btn-primary" href="' + helper.baseUrl + 'records/detail/' + response.data.appointment.urn + '">View Record</a>');
		    modal.show_modal();
			}
		});
},
    show_modal: function () {
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    },
}

var appointment = {
    init: function() {
        appointment.reload_table('appointment-table');
    },
    reload_table: function(table_name) {
                var table = "<table class='table table-striped table-bordered table-hover data-table'><thead><tr><th>Date</th><th>Company</th><th>Allocation</th><th>Created</th><th>Postcode</th></tr></thead>";
                table += "<tfoot><tr><th>Date</th><th>Company</th><th>Allocation</th><th>Created</th><th>DaPostcodete</th></tr></tfoot></table>";

                $('#appointment-table').html(table);
                appointment.populate_table(table_name);
				
				$(document).on('click','.data-table tbody tr',function(){
					modal.show_appointment($(this).attr('data-id'));
				});

    },
    populate_table: function(table_name) {
	
        var table = $('.data-table').DataTable({
            "dom": '<"top">p<"dt_info"i>rt<"bottom"lp><"clear">',
            "oLanguage": {
                "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
            },
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            //ordering:  false,
            "iDisplayLength": 10,
            stateSave: true,
            responsive:true,
            "ajax": {
                url: helper.baseUrl + "appointments/appointment_data",
                type: 'POST',
                beforeSend: function() {
                    $('.dt_info').hide();
                },
				data: function (d){
				d.extra_field = false;
           		 },
                complete: function() {
                    $('.dt_info').show();
                    $('.tt').tooltip();		
                }
            },
 "columns": [{
                "data": "start"
            }, { 
                "data": "name"
            },  {
                "data": "attendee"
            }, {
                "data": "date_added"
            }, {
                "data": "postcode" 
            }],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4],
                "data": null,
                "defaultContent": "na"
            }
        ],
                "createdRow":function(row,data,dataIndex){
					$(row).attr('data-id',data['appointment_id']);
					$(row).addClass('pointer');
                       if(data['change_type']=="delete"){
                   $(row).addClass( 'danger' );
        }
                          
            }
        });

$(document).on('click','.reload-table',function(){
            table.draw();
    });

        //filterable columns
        // Setup - adds search input boxes to the footer row
        $('.data-table tfoot th').each(function() {
            var title = $('.data-table thead th').eq($(this).index()).text();
            if (title == "Options") {
                $(this).html('');
            } else {
                var search_val = table.column($(this).index()).search();
				//console.log(table.column($(this).index()).search());
                $(this).html('<input class="dt-filter form-control" style="width:100%" placeholder="Filter..." value="' + search_val[0] + '" />');
            }
        });

        // Apply the search
        table.columns().eq(0).each(function(colIdx) {
            $('input', table.column(colIdx).footer()).on('keyup change', function() {
                table
                    .column(colIdx)
                    .search(this.value)
                    .draw();
            });
        });
        //this moves the search input boxes to the top of the table
		
        var r = $('.data-table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', 8);
        });
        $('.data-table thead').append(r);
        $('#search_0').css('text-align', 'center');
    }



}

</script>