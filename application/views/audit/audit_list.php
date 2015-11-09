<div class="row">
    <div class="col-lg-12 col-md-12">
       <div id="audit-table">
          </div>
       </div>
        
    </div>


<script>
$(document).ready(function(){
audit.init();
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
	show_audit:function(id){
        $('.modal-title').text('Audit #' +id);
          $.ajax({
            url: helper.baseUrl + 'audit/audit_modal',
            type: "POST",
            dataType: "JSON",
            data: {id:id}
        }).done(function (response) {
			if(response.success){
				$('.modal-title').append('<span class="pull-right">'+response.data.audit.date_formatted+' </span>');
			var modal_html="";
			modal_html += "<h4>"+response.data.audit.title+"</h4>"
			modal_html += "<table class='table'><thead><tr><th>Field</th><th>Old Value</th><th>New Value</th></tr></thead><tbody>";
			$.each(response.data.values,function(k,change){ 
			modal_html += '<tr><td>'+change.column_name + '</td><td>' +change.oldval+'</td><td>' +change.newval+'</td></tr>';
			});
			modal_html += "</tbody></table>";
			$('#modal').find('.modal-body').html(modal_html);
			modal.clear_buttons();
			$('#modal').find('.modal-footer').append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
			$('#modal').find('.modal-footer').append('<a class="btn btn-primary" href="' + helper.baseUrl + 'records/detail/' + response.data.audit.urn + '">View Record</a>');
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

var audit = {
    init: function() {
        audit.reload_table('audit-table');
    },
    reload_table: function(table_name) {
                var table = "<table class='table table-striped table-bordered table-hover data-table'><thead><tr><th>Campaign</th><th>URN</th><th>Table</th><th>Type</th><th>User</th><th>Date</th></tr></thead>";
                table += "<tfoot><tr><th>Campaign</th><th>URN</th><th>Table</th><th>Type</th><th>User</th><th>Date</th></tr></tfoot></table>";

                $('#audit-table').html(table);
                audit.populate_table(table_name);
				
				$(document).on('click','.data-table tbody tr',function(){
					modal.show_audit($(this).attr('data-id'));
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
                url: helper.baseUrl + "audit/audit_data",
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
                "data": "campaign_name"
            },{
                "data": "urn"
            }, { 
                "data": "table_name"
            },  {
                "data": "change_type"
            }, {
                "data": "name"
            }, {
                "data": "timestamp" 
            }],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5],
                "data": null,
                "defaultContent": "na"
            }
        ],
                "createdRow":function(row,data,dataIndex){
					$(row).attr('data-id',data['audit_id']);
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