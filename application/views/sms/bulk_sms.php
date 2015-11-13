<div class="page-header">
    <h2>
        Batch Sms Tool
    </h2>
</div>

<div class="panel panel-primary email-panel">

    <div class="panel-heading">
        <h4 class="panel-title">
            Setup bulk sms send
        </h4>
    </div>
    <div class="panel-body">
        <form>
            <div class="form-group">
                <input type="hidden" name="template_sender_id" value=""/>
                <input type="hidden" name="template_text" value=""/>
                <?php if(count($templates)>0){ ?>
                    <p>Please choose the template you want to use</p>
                    <select id="template_id" name="template_id" class="selectpicker">
                        <option value="">--Select template--</option>
                        <?php foreach($templates as $row): ?>
                            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                <?php } else { ?>
                    <p>No sms templates have been configured for this campaign</p>
                <?php } ?>
            </div>
            <div class="form-group">
                <label>URN List</label><br>
                <textarea id="urns" name="urns" class="form-control"
                          placeholder="Paste in the record URNs you want to send to seperated by lines or commas"></textarea>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group input-group-sm">
                        <input type="hidden" name="sender" value=""/>
                        <p>From</p>
                        <select name="sender_id" class="selectpicker" id="sender_select" data-width="100%" data-size="5">
                            <option value="">Nothing selected</option>
                            <?php foreach ($sms_senders as $row): ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="other-sender">
      
                    </div>

                </div>
            </div>
            <div class="row send_to">
                <div class="col-xs-12">
                    <div class="form-group input-group-sm">
                        <input type="hidden" name="sent_to[]" value=""/>
                    </div>
                </div>
            </div>
            <div class="form-group input-group-sm">
                <p>Text (<span id="chars">305</span> characters remaining...)</p>
                <textarea class="form-control" title="Enter the message" name="text" required style="width: 1112px; height: 298px;" maxlength="305"></textarea>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" id="send">Send</button>
                <span id="wait" style="display:none"><img
                        src="<?php echo base_url() ?>assets/img/ajax-loader.gif"/></span>
            </div>
        </form>


    </div>

</div>
<script>
    $(document).ready(function () {

        //Max length for sms text
        var maxLength = 305;
        $('textarea[name="text"]').keyup(function() {
            var length = $(this).val().length;
            var length = maxLength-length;
            $('#chars').text(length);
        });

        $('#template_id').on('change', function(){
            var template_id = $(this).val();

            if (template_id) {
                $.ajax({
                    url: helper.baseUrl + 'smstemplates/template_data',
                    type: "POST",
                    data: {id: template_id},
                    dataType: "JSON"
                }).done(function (response) {
                    if (response.success) {
                        $('form').find('input[name="template_sender_id"]').val(response.data.sender_id);
                        $('form').find('input[name="template_text"]').val(response.data.template_text);

                        $('#sender_select').selectpicker('val', response.data.sender_id).selectpicker('refresh');
                        $('#sender_select').prop('disabled', true);
                        $('#sender_select').selectpicker('refresh');

                        $('form').find('textarea[name="text"]').val(response.data.template_text);
                        $('#chars').text(305-$('form').find('textarea[name="text"]').val().length);
                        $('form').find('textarea[name="text"]').prop('disabled', true);
						if(response.data.sender_id=="0"){
						             $('#other-sender').html('<input readonly type="text" value="'+response.data.custom_sender+'" class="form-control" style="width:50px" name="sender_field" />');	
						} else {
						 $('#other-sender').empty();	
						}
                    }
                });
            }
            else {
                $('form').find('input[name="template_sender_id"]').val('');
                $('form').find('input[name="template_text"]').val('');

                $('#sender_select').selectpicker('val', '').selectpicker('refresh');
                $('#sender_select').prop('disabled', false);
                $('#sender_select').selectpicker('refresh');

                $('form').find('textarea[name="text"]').val('');
                $('#chars').text(305-$('form').find('textarea[name="text"]').val().length);
                $('form').find('textarea[name="text"]').prop('disabled', false);
            }
        });

        $('#sender_select').on('change', function(){
            var text = $("select[name=sender_id] option[value='"+$(this).val()+"']").text();
            $('form').find('input[name="sender"]').val(text);
        });

        $(document).on('click', '#send', function (e) {
            e.preventDefault();
            var urns =  $('#urns').val();
            var sender_id = $('#sender_select').selectpicker('val');
            var text = $('form').find('textarea[name="text"]').val();
            var template_sender_id = $('form').find('input[name="template_sender_id"]').val();
            var template_text = $('form').find('input[name="template_text"]').val();
            if ($('#urns').val() !== "") {
                if ((sender_id !== "" || template_sender_id !== "") && (text !== "" || template_text !== "")) {
                    $.ajax({
                        url: helper.baseUrl + 'sms/send_bulk_sms',
                        type: "POST",
                        dataType: "JSON",
                        data: $('form').serialize(),
                        beforeSend: function () {
                            $('#send').hide();
                            $('#wait').show();
                        }
                    }).done(function (response) {
                        if (response.success) {
                            if (response.test) {
                                flashalert.warning(response.msg);
                            }
                            else {
                                flashalert.success(response.msg);
                            }
                            $('#send').show();
                            $('#wait').hide();
                            $('form')[0].reset();
                            $('#sender_select').selectpicker('val', '').prop('disabled', false).selectpicker('refresh');
                            $('#template_id').selectpicker('val', '').prop('disabled', false).selectpicker('refresh');
                        }
                        else {
                            flashalert.danger(response.msg);
                            $('#send').show();
                            $('#wait').hide();
                        }
                    });
                } else {
                    flashalert.danger("Please set all the fields in the form or select a template");
                }
            } else {
                flashalert.danger("Please input some record URNs to the list");
            }
        });
    });
</script>