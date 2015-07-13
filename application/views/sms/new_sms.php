<div class="page-header">
    <h2>
        New SMS
        <small>URN: <?php echo $urn ?></small>
    </h2>
</div>

<div class="panel panel-primary contact-panel">
    <!-- Default panel contents -->
    <div class="panel-heading">
        <h4 class="panel-title">
            SMS
            <span class="glyphicon glyphicon-question-sign pull-right tt" data-toggle="tooltip" data-html="true"
                  data-placement="top"
                  title="Please complete all the fields. When you are finished click the send button below"></span>
        </h4>
    </div>

    <div class="panel-body">
        <form role="form">
            <input type="hidden" name="urn" value="<?php echo $urn ?>"/>
            <input type="hidden" name="template_id" value="<?php echo $template_id ?>"/>
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id'] ?>"/>

            <div class="row">
                <div class="col-xs-8">
                    <div class="form-group input-group-sm">
                        <p>Please set the template name</p>
                        <input type="text" class="form-control" name="template_name"
                               title="Enter the template name"
                               value="<?php echo $template['template_name']; ?>" required/>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="form-group pull-right">
                        <label for="type">Include unsubscribe message</label>
                        <br>

                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-info btn-sm">
                                <input type="radio" name="template_unsubscribe" value="1" autocomplete="off"
                                       id="unsubscribe-yes">Yes
                            </label>
                            <label class="btn btn-info btn-sm renewal-label">
                                <input type="radio" name="template_unsubscribe" value="0" autocomplete="off"
                                       id="unsubscribe-no">No
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="form-group input-group-sm">
                        <p>From</p>
                        <input type="text" class="form-control" name="template_from"
                               title="from field"
                               value="<?php echo $template['template_from']; ?>" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="form-group input-group-sm">
                        <p>Sent To</p>
                        <select name="sent_to[]" class="selectpicker" id="numbers_select" data-width="100%"
                                data-size="5"
                                multiple>
                            <?php foreach ($numbers as $row): ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group input-group-sm">
                <p>Text (<span id="chars"><?php echo 160 - strlen($template['template_text'])?></span> characters remaining...)</p>
        <textarea class="form-control" title="Enter the sms text" name="template_text" required style="width: 1112px; height: 298px;" maxlength="160">
            <?php echo $template['template_text']; ?>
        </textarea>
            </div>

            <!-- SUBMIT AND CANCEL BUTTONS -->
            <div class="form-actions pull-right">
                <button class="marl btn btn-default close-btn">Cancel</button>
                <button type="submit" class="marl btn btn-primary send-sms">Send</button>
            </div>

        </form>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var urn = '<?php echo $urn ?>';
        sms.init(urn);
    });
</script>