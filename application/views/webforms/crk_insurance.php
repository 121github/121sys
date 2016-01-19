<?php
$show_footer = false;
if (isset($_SESSION['current_campaign']) && in_array("show footer", $_SESSION['permissions'])) {
    $show_footer = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>Liabilty Insurance Questions</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/themes/colors/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/bootstrap-theme.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/css/dataTables.bootstrap.css">
    <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slider.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/default.css">
    <!-- Set the baseUrl in the JavaScript helper -->
    <?php //load specific javascript files set in the controller
    if (isset($css)):
        foreach ($css as $file): ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/<?php echo $file ?>">
        <?php endforeach;
    endif; ?>
    <link rel="shortcut icon"
          href="<?php echo base_url(); ?>assets/themes/colors/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/icon.png">
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/wavsurfer.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.numeric.min.js"></script>
    <!--Need to make a new icon for this
          <link rel="apple-touch-icon" href="http://www.121system.com/assets/img/apple-touch-icon.png" />-->
    <style>
        .tooltip-inner {
            max-width: 450px;
            /* If max-width does not work, try using width instead */
            width: 450px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Liabilty Insurance Questions</h2>

    <p>Please complete the following questions and click save</p>

    <form id="form" style="padding-bottom:50px;">

     
        <div>
            <label>Legal trading status?</label>
            <br>
            <select name="answers[a24][]" class="selectpicker" data-width="100%"
                    data-size="5">
                <option <?php if (@strpos($values['a24'], "Limited") !== false) {
                    echo "selected";
                } ?>value='Limited'>Limited
                </option>
                <option <?php if (@strpos($values['a24'], "Partnership") !== false) {
                    echo "selected";
                } ?> value='Partnership'>Partnership
                </option>
                <option <?php if (@strpos($values['a24'], "Public Limited") !== false) {
                    echo "selected";
                } ?> value='Public Limited'>Public Limited
                </option>
                <option <?php if (@strpos($values['a24'], "Charity") !== false) {
                    echo "selected";
                } ?> value='Charity'>Charity
                </option>
                <option <?php if (@strpos($values['a24'], "Religious Organization") !== false) {
                    echo "selected";
                } ?> value='Religious Organization'>Religious Organization
                </option>
                <option <?php if (@strpos($values['a24'], "Sole Trader") !== false) {
                    echo "selected";
                } ?> value='Sole Trader'>Sole Trader
                </option>
                <option <?php if (@strpos($values['a24'], "Society") !== false) {
                    echo "selected";
                } ?> value='Society'>Society
                </option>
            </select>
        </div>
        <div class="form-group relative">
            <label>Policy Start Date</label>
            <br>
            <input type="text" name="answers[a3]" class="form-control date" placeholder="Set date"
                   value="<?php echo @$values['a3'] ?>"/>
        </div>
        <script type="text/javascript">
            /*$(document).ready(function(){
             $('.dob').datetimepicker({
             viewMode: 'years',
             format: 'DD/MM/YYYY',
             defaultDate: new Date(1979, 0, 1,1, 0, 0, 0),
             }).on('keypress paste', function (e) {
             e.preventDefault();
             return false;
             });
             });
             */
        </script>
 <div class="form-group relative">
            <label>Year Established</label>
            <br>
            <input type="text" name="answers[a4]" class="form-control dateyear" placeholder="Set date"
                   value="<?php echo @$values['a4'] ?>"/>
        </div>
                          <div class="form-group">
            <label>How many years experience?</label>
            <br>
            <input type="text" name="answers[a5]" class="form-control"
                   placeholder="Enter the number of years" value="<?php echo @$values['a5'] ?>"/>
        </div>
        <div class="form-group">
            <label>Estimated wage roll for the next 12 months</label>
            <br>
            <input type="text" name="answers[a6]" class="form-control"
                   placeholder="Enter the wage roll" value="<?php echo @$values['a6'] ?>"/>
        </div>
               <div class="form-group">
            <label>Previous Insurer</label>
            <br>
            <input type="text" name="answers[a7]" class="form-control"
                   placeholder="Enter the previous insurer" value="<?php echo @$values['a7'] ?>"/>
        </div>
          <div class="form-group">
            <label>What is your primary trade?</label>
            <br>
            <input type="text" name="answers[a8]" class="form-control"
                   placeholder="Enter the primary trade" value="<?php echo @$values['a8'] ?>"/>
        </div>
                 <div class="form-group">
            <label>Do you have a secondary trade?</label>
            <br>
            <input type="text" name="answers[a9]" class="form-control"
                   placeholder="Enter the secondary trade" value="<?php echo @$values['a9'] ?>"/>
        </div>

          <div class="form-group">
            <label>Previous Insurer</label>
            <br>
            <input type="text" name="answers[a10]" class="form-control"
                   placeholder="Enter the previous insurer" value="<?php echo @$values['a10'] ?>"/>
        </div>
                   <div class="form-group">
            <label>How many people need cover? (exlcuding directors)</label>
            <br>
            <input type="text" name="answers[a13]" class="form-control"
                   placeholder="Enter the number of people" value="<?php echo @$values['a13'] ?>"/>
        </div>
        <div class="question">
            <label>Do you require cover for tools?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a11][]" id="optionsRadios1" data-show-notes="true"
                           value="Yes" <?php if (@strpos($values['a11'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[11][]" id="optionsRadios2" 
                           value="No" <?php if (@strpos($values['a11'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>What is the maximum value of the tools?</label>
                <input type="text" name="answers[a12]" class="form-control"
                       placeholder="Enter the tools value" value="<?php echo @$values['a12'] ?>"/>
            </div>
        </div>
        <div class="question">
            <label>Do you use any heat equipment?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a14][]" id="optionsRadios1" 
                           value="Yes" <?php if (@strpos($values['a14'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a14][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a14'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>Please enter any notes</label>
                <input type="text" name="answers[a15]" value="<?php echo @$values['a15'] ?>" class="form-control"
                       placeholder="Enter notes if they answered no above "/>
            </div>
        </div>

              <div>
            <label>Activity details?</label>
            <br>
            <select name="answers[a17][]" class="selectpicker" data-width="100%"
                    data-size="5">
                <option <?php if (@strpos($values['a17'], "Clerical") !== false) {
                    echo "selected";
                } ?>value='Clerical'>Clerical
                </option>
                <option <?php if (@strpos($values['a17'], "Consulting") !== false) {
                    echo "selected";
                } ?> value='Consulting'>Consulting
                </option>
                <option <?php if (@strpos($values['a17'], "Wood work") !== false) {
                    echo "selected";
                } ?> value='Wood work'>Wood work
                </option>
                <option <?php if (@strpos($values['a17'], "Manual") !== false) {
                    echo "selected";
                } ?> value='Manual'>Manual
                </option>
                                <option <?php if (@strpos($values['a17'], "Non-manual") !== false) {
                    echo "selected";
                } ?> value='Non-manual'>Non-manual
                </option>
                <option <?php if (@strpos($values['a17'], "Consulting") !== false) {
                    echo "selected";
                } ?> value='Consulting'>Consulting
                </option>
                <option <?php if (@strpos($values['a17'], "Surveying") !== false) {
                    echo "selected";
                } ?> value='Surveying'>Surveying
                </option>


            </select>
        </div>
         <div class="form-group">
            <label>Public liability required (&pound;)</label>
            <br>
            <input type="text" name="answers[a18]" class="form-control"
                   placeholder="Enter the amount" value="<?php echo @(empty($values['a18'])?"10,000,000":$values['a18']) ?>"/>
        </div>
               <div class="form-group">
            <label>Employers liability required (&pound;)</label>
            <br>
            <input type="text" name="answers[a19]" class="form-control"
                   placeholder="Enter the amount"  value="<?php echo @(empty($values['a19'])?"2,000,000":$values['a19']) ?>"/>
        </div>
        <div class="form-group">
            <label>Any other information relevant</label>
            <br>
            <textarea class="form-control" style="height:50px"
                      name="answers[a23][]"><?php echo @$values['a23'] ?></textarea>
        </div>



<hr>
<h3>Assumptions</h3>
<b>
The following assumptions have been made and will form the basis of the quote provided:</b>
<ul>
<li>You do not have a separate dedicated business premises.</li>
<li>Your work does not involve discharge of fumes, effluent or anything of a noxious nature.</li>
<li>Your work does not involve the use of substances which could be harmful to health.</li>
<li>There have been NO losses or incidents giving rise to losses in the last 5 years. (This includes all claims, incidents or losses made by you or against you, your directors or your partners irrespective of whether a claim was made or not.)</li>
</ul>
<b>No proposer / director / partner of the business / practice has ever:</b>
<ul><li>been declared bankrupt or insolvent.
<li>been the subject of bankruptcy proceedings.</li>
<li>had a proposal refused or declined.</li>
<li>had a renewal refused.</li>
<li>had an insurance cancelled.</li>
<li>had special terms imposed.</li>
<li>has non-motor convictions or criminal offences.</li>
<li>has non-motor prosecutions pending.</li>
</ul>
  <div class="question">
            <label>Do you agree?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a20][]" id="optionsRadios1" 
                           value="Yes" <?php if (@strpos($values['a20'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a20][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a20'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            </div>
            
                    <a href="<?php echo base_url() . 'records/detail/' . $this->uri->segment(4); ?>" class="btn btn-default">Go
            back</a>
        <?php if (@!empty($values['completed_on'])) { ?>
            <button id="save-form" class="btn btn-primary">Save form</button>
        <?php } else { ?>
            <button id="complete-form" class="btn btn-primary">Save form</button>
        <?php } ?>
    </form>
    </div>
<div class="page-success alert alert-success hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-info alert alert-info hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-warning alert alert-warning hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-danger alert alert-danger hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-slider.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/browser/jquery.browser.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/modals.js"></script>
<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
<script>
    $(document).ready(function () {
		$('.dateyear').datetimepicker( {
    format: "YYYY", // Notice the Extra space at the beginning
 viewMode: 'years',
});
		
        $('[data-toggle="tooltip"]').tooltip()
        //hide all the notes

        $.each($('.question'), function () {
            $(this).find('.form-group').hide();
        });

        $.each($('[data-show-notes="true"]:checked'), function () {
            $(this).closest('.question').find('.form-group').show();
        });


        $(document).on('blur', 'input[type="text"]', function () {
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            })
        });

        $(document).on('change', 'select,input[type="radio"]', function () {
            console.log($(this).attr('data-show-notes'));
            if ($(this).attr('data-show-notes')) {
                $(this).closest('.question').find('.form-group').show();
            } else {
                $(this).closest('.question').find('.form-group').hide().find('input').val('');
            }

            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            })
        });
        $(document).on('click', '#save-form', function (e) {
            e.preventDefault();
            if (check_form()) {
                $.ajax({
                    type: "POST",
                    data: $('#form').serialize() + '&save=1'
                }).done(function (response) {
                    flashalert.success("Form was saved");
                });
            } else {
                flashalert.danger("Please answer all questions");
            }
        });
        function check_form() {
            var completed = true;
            $.each($('.question'), function () {
                if ($(this).find('input[type="radio"]:checked').length < 1) {
                    $(this).css('border', '1px solid red');
                    completed = false;
                }
            });
            return completed;

        }

        $(document).on('click', '#complete-form', function (e) {
            e.preventDefault();
            if (check_form()) {
                $.ajax({
                    type: "POST",
                    data: $('#form').serialize() + '&save=1&complete=1'
                }).done(function (response) {
                    flashalert.success("Form was saved");
                });
            } else {
                flashalert.danger("Please answer all questions");
            }
        });


    });
</script>

<?php //load specific javascript files set in the controller
if (isset($javascript)):
    foreach ($javascript as $file): ?>
        <script src="<?php echo base_url(); ?>assets/js/<?php echo $file ?>"></script>
    <?php endforeach;
endif; ?>
</body>
</html>