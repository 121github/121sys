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
    <title>Onion Data Capture</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/bootstrap-theme.css">
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
          href="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/icon.png">
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
    <h2>Onion Data Capture</h2>

    <p>Please complete the following questions and click save</p>

    <form id="form" style="padding-bottom:50px;">
        <label>Temporary or permanant contracts? <span class="glyphicon glyphicon-question-sign tt"
                                                       data-toggle="tooltip" data-placement="right"
                                                       title="Whether or not the recruitment company places `temps` i.e. Contracts for a fixed duration rather than placing permenant employees, some business will do both!"></span></label>

        <div class="radio">
            <label>
                <input type="checkbox" name="answers[a1][]" id="optionsRadios1"
                       value="temporary"  <?php if (@strpos($values['a1'], "temporary") !== false) {
                    echo "checked";
                } ?> />
                Temporary
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="checkbox" name="answers[a1][]" id="optionsRadios2"
                       value="permanant" <?php if (@strpos($values['a1'], "permanant") !== false) {
                    echo "checked";
                } ?>>
                Permanant
            </label>
        </div>

        <label>Industry Sector(s)? <span class="glyphicon glyphicon-question-sign tt" data-toggle="tooltip"
                                         data-width="500" data-html="true" data-placement="right"
                                         title="The types of contracts that the business places, the can fall into the categories below. Some sectors are not worth persuing <span class='red'>(shown in red)</span>"></span></label>
        <table>
            <tr>
                <td>
                    <div class="radio"><label for="sectorsCheck1">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck1"
                                   value="Banking/Finance/Legal" <?php if (@strpos($values['a2'], "Banking") !== false) {
                                echo "checked";
                            } ?>>
                            Banking/Finance/Legal
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck2">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck2"
                                   value="Health/Social Care/Hospitality" <?php if (@strpos($values['a2'], "Health") !== false) {
                                echo "checked";
                            } ?>>
                            Health/<span class="red">Social Care/Hospitality</span>
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck3">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck3"
                                   value="Office Administration" <?php if (@strpos($values['a2'], "Office") !== false) {
                                echo "checked";
                            } ?>>
                            Office Administration
                        </label></div>
                </td>
            </tr>


            <tr>
                <td>
                    <div class="radio"><label for="sectorsCheck4">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck4"
                                   value="Biotechnology" <?php if (@strpos($values['a2'], "Biotechnology") !== false) {
                                echo "checked";
                            } ?>>
                            Biotechnology
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck5">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck5"
                                   value="Hospitality" <?php if (@strpos($values['a2'], "Hospitality") !== false) {
                                echo "checked";
                            } ?>>
                            <span class="red">Hospitality</span>
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck6">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck6"
                                   value="Secretarial" <?php if (@strpos($values['a2'], "Secretarial") !== false) {
                                echo "checked";
                            } ?>>
                            <span class="red">Secretarial</span>
                        </label></div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="radio"><label for="sectorsCheck7">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck7"
                                   value="IT" <?php if (@strpos($values['a2'], "IT") !== false) {
                                echo "checked";
                            } ?>>
                            IT
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck8">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck8"
                                   value="Sales/Retail" <?php if (@strpos($values['a2'], "Retail") !== false) {
                                echo "checked";
                            } ?>>
                            <span class="red">Sales/Retail</span>
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck9">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck9"
                                   value="Construction/Property" <?php if (@strpos($values['a2'], "Construction") !== false) {
                                echo "checked";
                            } ?>>
                            Construction/Property
                        </label></div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="radio"><label
                            for="sectorsCheck10" <?php if (@strpos($values['a2'], "Health") !== false) {
                            echo "checked";
                        } ?>>
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck10"
                                   value="Interim/Change" <?php if (@strpos($values['a2'], "Interim") !== false) {
                                echo "checked";
                            } ?>>
                            Interim/Change
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck11">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck11"
                                   value="Security" <?php if (@strpos($values['a2'], "Security") !== false) {
                                echo "checked";
                            } ?>>
                            <span class="red">Security</span>
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck12">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck12"
                                   value="Consultancy" <?php if (@strpos($values['a2'], "Consultancy") !== false) {
                                echo "checked";
                            } ?>>
                            Consultancy
                        </label></div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="radio"><label for="sectorsCheck13">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck13"
                                   value="Management" <?php if (@strpos($values['a2'], "Management") !== false) {
                                echo "checked";
                            } ?>>
                            Management
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck14">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck14"
                                   value="Teaching/Education" <?php if (@strpos($values['a2'], "Teaching") !== false) {
                                echo "checked";
                            } ?>>
                            <span class="red">Teaching/Education</span>
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck15">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck15"
                                   value="Drivers" <?php if (@strpos($values['a2'], "Drivers") !== false) {
                                echo "checked";
                            } ?>>
                            <span class="red">Drivers</span>
                        </label></div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="radio"><label for="sectorsCheck16">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck16"
                                   value="Media/Digital/Creative" <?php if (@strpos($values['a2'], "Media") !== false) {
                                echo "checked";
                            } ?>>
                            Media/Digital
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck17">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck17"
                                   value="Telecommunication" <?php if (@strpos($values['a2'], "Telecommunication") !== false) {
                                echo "checked";
                            } ?>>
                            Telecommunication
                        </label></div>
                </td>
                <td>
                    <div class="radio"><label for="sectorsCheck18">
                            <input type="checkbox" name="answers[a2][]" id="sectorsCheck18"
                                   value="Engineering" <?php if (@strpos($values['a2'], "Engineering") !== false) {
                                echo "checked";
                            } ?>>
                            Engineering
                        </label></div>
                </td>
            </tr>
        </table>
        <hr>
        <p>If the recruitment company <b>does</b> place temps <b>and</b> is working in a sector worth pursing then we
            also need to ask the following questions</p>

        <div class="form-group">
            <label for="employees">Number of employees <span class="glyphicon glyphicon-question-sign tt"
                                                             data-toggle="tooltip" data-width="500" data-html="true"
                                                             data-placement="right"
                                                             title="The number of people directly employed by the recruitment company, this will help determine the size and likely type of business eg.SME owner managed"></span></label>
            <input name="answers[a3]" class="form-control" id="employees"
                   placeholder="Enter the number of employees or an estimation" value="<?php echo @$values['a3'] ?>">
        </div>

        <div class="form-group">
            <label for="contractors">Number of temp contractors <span class="glyphicon glyphicon-question-sign tt"
                                                                      data-toggle="tooltip" data-width="500"
                                                                      data-html="true" data-placement="right"
                                                                      title="the number of contractors out on a week to week basis on temp contracts, not the number registered with them. Not including any permanants."></span></label>
            <input name="answers[a4]" class="form-control" id="contractors"
                   placeholder="Enter the number of temp contractors" value="<?php echo @$values['a4'] ?>">
        </div>

        <div class="form-group">
            <label for="rates">Average weekly contract rates <span class="glyphicon glyphicon-question-sign tt"
                                                                   data-toggle="tooltip" data-width="500"
                                                                   data-html="true" data-placement="right"
                                                                   title="An indication of the range of weekly contract rates, the higher the rates the more relevant Onion accountancy services will be"></span></label>
            <input name="answers[a5]" class="form-control" id="rates"
                   placeholder="Enter the average weekly contract rates" value="<?php echo @$values['a5'] ?>">
        </div>
        <hr>
        <p>The above questions are the minimum we need, if possible please ask the questions below</p>

        <div class="form-group">
            <label for="how">How do contractors work? <span class="glyphicon glyphicon-question-sign tt"
                                                            data-toggle="tooltip" data-width="500" data-html="true"
                                                            data-placement="right"
                                                            title="Eg. PAYE,Umbrella,PSC? How they allow contractors to work will give us an idea of whether they are completely new to the concept of a PSC or if they already have experience."></span></label>
            <!--            <input name="answers[a6]" class="form-control" id="how"-->
            <!--                   placeholder="Enter the ways they allow contractors to work, any payment schemes used"-->
            <!--                   value="--><?php //echo @$values['a6'] ?><!--">-->
            <select name="answers[a6][]" class="selectpicker" id="how_contractors_select" data-width="100%"
                    data-size="5" multiple>
                <option value="Limited Company (PSC)">Limited Company (PSC)</option>
                <option value="Umbrella">Umbrella</option>
                <option value="Employee (PAYE)">Employee (PAYE)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="competitors">Competitors? <span class="glyphicon glyphicon-question-sign tt"
                                                        data-toggle="tooltip" data-width="500" data-html="true"
                                                        data-placement="right"
                                                        title="Are they already working with any of Onions competitors? If so, who are they up against."></span></label>
            <input name="answers[a7]" class="form-control" id="how"
                   placeholder="Enter any other accountancy firms the recruiter may deal with"
                   value="<?php echo @$values['a7'] ?>">
            <!--            <select  name="answers[a7]" class="selectpicker" id="competitors_select" data-width="100%" data-size="5">-->
            <!--                <option value="" >Nothing selected</option>-->
            <!--                <option value="Brookson Limited" >Brookson Limited</option>-->
            <!--                <option value="Contractor Genie" >Contractor Genie</option>-->
            <!--                <option value="Danbro" >Danbro</option>-->
            <!--                <option value="Income Made Smart (IMS)" >Income Made Smart (IMS)</option>-->
            <!--                <option value="Liberty Bishop" >Liberty Bishop</option>-->
            <!--                <option value="SJD Accountancy" >SJD Accountancy</option>-->
            <!--            </select>-->
        </div>

        <div class="form-group">
            <input type="checkbox" name="answers[a8]" value="true" <?php if (@$values['a8']) { echo 'checked'; } ?>>
            <label for="psls">Uses PSL? <span class="glyphicon glyphicon-question-sign tt"
                                                             data-toggle="tooltip" data-width="500" data-html="true"
                                                             data-placement="right"
                                                             title="If they have a psl, when is it due to be renewed and who is responsible for that?"></span></label>
<!--            <input name="answers[a8]" class="form-control" id="psls" placeholder="Eneter the PSL review person here"-->
<!--                   value="--><?php //echo @$values['a8'] ?><!--">-->
        </div>

        <div class="form-group">
            <label for="psls">PSL Review Person? </label>
            <input name="answers[a9]" class="form-control" id="psls" maxlength="100" placeholder="Eneter the person who is responsible (if know and if there is one) for reviewing the PSL here"
                   value="<?php echo @$values['a9'] ?>">
        </div>

        <div class="form-group">
            <label for="psls">PSL Review Date? </label>
            <input name="answers[a10]" class="form-control date" id="psls" placeholder="Eneter the PSL review date here"
                   value="<?php echo @$values['a10'] ?>">
        </div>


        <a href="<?php echo base_url() . 'records/detail/' . $this->uri->segment(4); ?>" class="btn btn-default">Go
            back</a>
        <button type="submit" id="save-form" class="btn btn-primary">Save form</button>

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
<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
<script>
    $(document).ready(function () {
        $('form').find('input[name="answers[a3]"]').numeric();
        $('form').find('input[name="answers[a4]"]').numeric();
        $('form').find('input[name="answers[a5]"]').numeric();
        $('#how_contractors_select').selectpicker('val', (<?php echo "'".@$values['a6']."'" ?>).split(',')).selectpicker('render');
        $('#competitors_select').selectpicker('val', (<?php echo "'".@$values['a7']."'" ?>).split(',')).selectpicker('render');

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $(document).on('blur', 'input', function () {
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            })
        });

        $(document).on('change', 'input[type="checkbox"]', function () {
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            })
        });
        $(document).on('click', '#save-form', function (e) {
            console.log($('#form').serialize());
            e.preventDefault();
            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1'
            }).done(function (response) {
                flashalert.success("Form was saved");
            });
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