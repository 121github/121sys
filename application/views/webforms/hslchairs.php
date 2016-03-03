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
    <title>HSL Pre-Consultation Checklist</title>
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
    <h2>HSL Pre-Consultation Checklist</h2>

    <p>Please complete the following questions and click save</p>

    <form id="form" style="padding-bottom:50px;">
      <input type="hidden" name="id" value="<?php echo @$values['id'] ?>" />
        <div>
            <label>Please enter the origin/source of the lead</label>
            <br>
            <select name="answers[a25]" class="selectpicker" data-width="100%"
                    data-size="5">
                <option value=''>--Please select--</option>
                <option <?php if (@strpos($values['a25'], "Loop phone") !== false) {
                    echo "selected";
                } ?> value='Loop phone'>Loop phone
                </option>
                <option <?php if (@strpos($values['a25'], "Web") !== false) {
                    echo "selected";
                } ?> value='Web'>Web
                </option>
                <option <?php if (@strpos($values['a25'], "Direct Call") !== false) {
                    echo "selected";
                } ?> value='Direct Call'>Direct Call
                </option>
                <option <?php if (@strpos($values['a25'], "Direct Email") !== false) {
                    echo "selected";
                } ?> value='Direct Email'>Direct Email
                </option>
                <option <?php if (@strpos($values['a25'], "Customer Services") !== false) {
                    echo "selected";
                } ?> value='Customer Services'>Customer Services
                </option>
                <option <?php if (@strpos($values['a25'], "Other") !== false) {
                    echo "selected";
                } ?> value='Other'>Other
                </option>
            </select>
        </div>
        <h5>Now capture/validate the following information from the customer</h5>

        <div>
            <label style="width:50%">Where did you hear about us?</label><label style="width:50%">Secondary
                option</label>
            <br>
            <select name="answers[a24][]" id="hear" class="selectpicker" data-width="50%" multiple
                    data-size="5">
                <option <?php if (@strpos($values['a24'], "Direct Mail") !== false) {
                    echo "selected";
                } ?>value='Mail / Leaflet'>Mail / Leaflet
                </option>
                <option <?php if (@strpos($values['a24'], "Refferal (family, friend, neighbour)") !== false) {
                    echo "selected";
                } ?> value='Refferal (family, friend, neighbour)'>Refferal (family, friend, neighbour)
                </option>
                <option <?php if (@strpos($values['a24'], "Newspaper") !== false) {
                    echo "selected";
                } ?> value='Newspaper'>Newspaper
                </option>
                <option <?php if (@strpos($values['a24'], "Magazine") !== false) {
                    echo "selected";
                } ?> value='Magazine'>Magazine
                </option>
                <option <?php if (@strpos($values['a24'], "Website") !== false) {
                    echo "selected";
                } ?> value='Website'>Website
                </option>
                <option <?php if (@strpos($values['a24'], "Radio") !== false) {
                    echo "selected";
                } ?> value='Radio'>Radio
                </option>
                <option <?php if (@strpos($values['a24'], "Facebook") !== false) {
                    echo "selected";
                } ?> value='Facebook'>Facebook
                </option>
                <option <?php if (@strpos($values['a24'], "TV Spot") !== false) {
                    echo "selected";
                } ?> value='TV Spot'>TV Spot
                </option>
                <option <?php if (@strpos($values['a24'], "TV Sponsorship") !== false) {
                    echo "selected";
                } ?> value='TV Sponsorship'>TV Sponsorship
                </option>
                <option <?php if (@strpos($values['a24'], "Door") !== false) {
                    echo "selected";
                } ?> value='Door'>Door
                </option>
                <option <?php if (@strpos($values['a24'], "Doordrop") !== false) {
                    echo "selected";
                } ?> value='Doordrop'>Doordrop
                </option>
                <option <?php if (@strpos($values['a24'], "Previous Customer") !== false) {
                    echo "selected";
                } ?> value='Previous Customer'>Previous Customer
                </option>
                <option <?php if (@strpos($values['a24'], "Catalogue") !== false) {
                    echo "selected";
                } ?> value='Catalogue'>Catalogue
                </option>
                <option <?php if (@strpos($values['a24'], "OT Recommendation") !== false) {
                    echo "selected";
                } ?> value='OT Recommendation'>OT Recommendation
                </option>
                <option <?php if (@strpos($values['a24'], "Press Inserts") !== false) {
                    echo "selected";
                } ?> value='Press Inserts'>Press Inserts
                </option>
            </select>
            <select name="answers[a28][]" id="sub-hear" class="selectpicker" data-width="49%" multiple data-size="5">
                <optgroup label="Newspaper" disabled>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Mail Weekend") !== false) {
                            echo "selected";
                        } ?> value='Daily Mail Weekend'>Daily Mail Weekend
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Mail Midweek") !== false) {
                            echo "selected";
                        } ?> value='Daily Mail Midweek'>Daily Mail Midweek
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Mail Saturday") !== false) {
                            echo "selected";
                        } ?> value='Daily Mail Saturday'>Daily Mail Saturday
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Mail on Sunday Event") !== false) {
                            echo "selected";
                        } ?> value='Mail on Sunday Event'>Mail on Sunday Event
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Mail on Sunday / You Mag") !== false) {
                            echo "selected";
                        } ?> value='Mail on Sunday / You Mag'>Mail on Sunday / You Mag
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Saturday Express Mag") !== false) {
                            echo "selected";
                        } ?> value='Saturday Express Mag'>Saturday Express Mag
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Express Saturday") !== false) {
                            echo "selected";
                        } ?> value='Daily Express Saturday'>Daily Express Saturday
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Express Midweek") !== false) {
                            echo "selected";
                        } ?> value='Daily Express Midweek'>Daily Express Midweek
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sunday Express") !== false) {
                            echo "selected";
                        } ?> value='Sunday Express'>Sunday Express
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Telegraph Saturday") !== false) {
                            echo "selected";
                        } ?> value='Daily Telegraph Saturday'>Daily Telegraph Saturday
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Telegraph Midweek") !== false) {
                            echo "selected";
                        } ?> value='Daily Telegraph Midweek'>Daily Telegraph Midweek
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Saturday Telegraph Magazine") !== false) {
                            echo "selected";
                        } ?> value='Saturday Telegraph Magazine'>Saturday Telegraph Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sunday Telegraph") !== false) {
                            echo "selected";
                        } ?> value='Sunday Telegraph'>Sunday Telegraph
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Sun") !== false) {
                            echo "selected";
                        } ?> value='The Sun'>The Sun
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sun TV Mag") !== false) {
                            echo "selected";
                        } ?> value='Sun TV Mag'>Sun TV Mag
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sun on Sunday / TV Soap") !== false) {
                            echo "selected";
                        } ?> value='Sun on Sunday / TV Soap'>Sun on Sunday / TV Soap
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Times Saturday") !== false) {
                            echo "selected";
                        } ?> value='The Times Saturday'>The Times Saturday
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Times Midweek") !== false) {
                            echo "selected";
                        } ?> value='The Times Midweek'>The Times Midweek
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Times Mag") !== false) {
                            echo "selected";
                        } ?> value='The Times Mag'>The Times Mag
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Sunday Times") !== false) {
                            echo "selected";
                        } ?> value='The Sunday Times'>The Sunday Times
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Sunday Times Mag") !== false) {
                            echo "selected";
                        } ?> value='The Sunday Times Mag'>The Sunday Times Mag
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sunday Times Culture") !== false) {
                            echo "selected";
                        } ?> value='Sunday Times Culture'>Sunday Times Culture
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Mirror") !== false) {
                            echo "selected";
                        } ?> value='Daily Mirror'>Daily Mirror
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "We Love TV") !== false) {
                            echo "selected";
                        } ?> value='We Love TV'>We Love TV
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sunday Mirror") !== false) {
                            echo "selected";
                        } ?> value='Sunday Mirror'>Sunday Mirror
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sunday Mirror Notebook") !== false) {
                            echo "selected";
                        } ?> value='Sunday Mirror Notebook'>Sunday Mirror Notebook
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The People") !== false) {
                            echo "selected";
                        } ?> value='The People'>The People
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Love Sunday") !== false) {
                            echo "selected";
                        } ?> value='Love Sunday'>Love Sunday
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Guardian Weekend Mag") !== false) {
                            echo "selected";
                        } ?> value='Guardian Weekend Mag'>Guardian Weekend Mag
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Guardian") !== false) {
                            echo "selected";
                        } ?> value='The Guardian'>The Guardian
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Observer Magazine") !== false) {
                            echo "selected";
                        } ?> value='Observer Magazine'>Observer Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Observer") !== false) {
                            echo "selected";
                        } ?> value='The Observer'>The Observer
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Mail Scotland Saturday") !== false) {
                            echo "selected";
                        } ?> value='Daily Mail Scotland Saturday'>Daily Mail Scotland Saturday
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Mail on Sunday Scotland") !== false) {
                            echo "selected";
                        } ?> value='Mail on Sunday Scotland'>Mail on Sunday Scotland
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Mail Scotland Midweek") !== false) {
                            echo "selected";
                        } ?> value='Daily Mail Scotland Midweek'>Daily Mail Scotland Midweek
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Scottish Daily Express Saturday") !== false) {
                            echo "selected";
                        } ?> value='Scottish Daily Express Saturday'>Scottish Daily Express Saturday
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Scottish daily express midweek") !== false) {
                            echo "selected";
                        } ?> value='Scottish daily express midweek'>Scottish daily express midweek
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Scottish Sunday Express") !== false) {
                            echo "selected";
                        } ?> value='Scottish Sunday Express'>Scottish Sunday Express
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Daily Record") !== false) {
                            echo "selected";
                        } ?> value='Daily Record'>Daily Record
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sunday Mail") !== false) {
                            echo "selected";
                        } ?> value='Sunday Mail'>Sunday Mail
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sunday Post") !== false) {
                            echo "selected";
                        } ?> value='Sunday Post'>Sunday Post
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Herald Magazine") !== false) {
                            echo "selected";
                        } ?> value='The Herald Magazine'>The Herald Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sunday Herald") !== false) {
                            echo "selected";
                        } ?> value='Sunday Herald'>Sunday Herald
                    </option>
                </optgroup>
                <optgroup label="Magazine" disabled>
                    <option
                        <?php if (@strpos($values['a28'], "TV times code") !== false) {
                            echo "selected";
                        } ?> value='TV times code'>TV times code
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Radio times code") !== false) {
                            echo "selected";
                        } ?> value='Radio times code'>Radio times code
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Peoples Friend") !== false) {
                            echo "selected";
                        } ?> value='Peoples Friend'>Peoples Friend
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Peoples Friend Special") !== false) {
                            echo "selected";
                        } ?> value='Peoples Friend Special'>Peoples Friend Special
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Peoples friend Pocket Novels") !== false) {
                            echo "selected";
                        } ?> value='Peoples friend Pocket Novels'>Peoples friend Pocket Novels
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "My Weekly") !== false) {
                            echo "selected";
                        } ?> value='My Weekly'>My Weekly
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "My Weekly Special") !== false) {
                            echo "selected";
                        } ?> value='My Weekly Special'>My Weekly Special
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "My Weekly Pocket Novels") !== false) {
                            echo "selected";
                        } ?> value='My Weekly Pocket Novels'>My Weekly Pocket Novels
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Radio Times") !== false) {
                            echo "selected";
                        } ?> value='Radio Times'>Radio Times
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Radio times Extra") !== false) {
                            echo "selected";
                        } ?> value='Radio times Extra'>Radio times Extra
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Womens Weekly") !== false) {
                            echo "selected";
                        } ?> value='Womens Weekly'>Womens Weekly
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Womens Weekly Special") !== false) {
                            echo "selected";
                        } ?> value='Womens Weekly Special'>Womens Weekly Special
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Puzzler Big Brands") !== false) {
                            echo "selected";
                        } ?> value='Puzzler Big Brands'>Puzzler Big Brands
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Puzzler Q Range") !== false) {
                            echo "selected";
                        } ?> value='Puzzler Q Range'>Puzzler Q Range
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Puzzler Chat Pack") !== false) {
                            echo "selected";
                        } ?> value='Puzzler Chat Pack'>Puzzler Chat Pack
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Take A Break") !== false) {
                            echo "selected";
                        } ?> value='Take A Break'>Take A Break
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Take A Break Special") !== false) {
                            echo "selected";
                        } ?> value='Take A Break Special'>Take A Break Special
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Womans Own") !== false) {
                            echo "selected";
                        } ?> value='Womans Own'>Womans Own
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Womans Own Special") !== false) {
                            echo "selected";
                        } ?> value='Womans Own Special'>Womans Own Special
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Womans") !== false) {
                            echo "selected";
                        } ?> value='Womans'>Womans
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Womans Special") !== false) {
                            echo "selected";
                        } ?> value='Womans Special'>Womans Special
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "TV Choice") !== false) {
                            echo "selected";
                        } ?> value='TV Choice'>TV Choice
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Total TV Guide") !== false) {
                            echo "selected";
                        } ?> value='Total TV Guide'>Total TV Guide
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Whats on TV") !== false) {
                            echo "selected";
                        } ?> value='Whats on TV'>Whats on TV
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "TV and Satelite Week") !== false) {
                            echo "selected";
                        } ?> value='TV and Satelite Week'>TV and Satelite Week
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Weekly News") !== false) {
                            echo "selected";
                        } ?> value='Weekly News'>Weekly News
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Yours") !== false) {
                            echo "selected";
                        } ?> value='Yours'>Yours
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Amateur Gardening ") !== false) {
                            echo "selected";
                        } ?> value='Amateur Gardening '>Amateur Gardening
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Garden News") !== false) {
                            echo "selected";
                        } ?> value='Garden News'>Garden News
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Saga") !== false) {
                            echo "selected";
                        } ?> value='Saga'>Saga
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Choice Magazine") !== false) {
                            echo "selected";
                        } ?> value='Choice Magazine'>Choice Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "BBC Countrylife") !== false) {
                            echo "selected";
                        } ?> value='BBC Countrylife'>BBC Countrylife
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Take A Puzzle") !== false) {
                            echo "selected";
                        } ?> value='Take A Puzzle'>Take A Puzzle
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Take A Crossword") !== false) {
                            echo "selected";
                        } ?> value='Take A Crossword'>Take A Crossword
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Arrowwords") !== false) {
                            echo "selected";
                        } ?> value='Arrowwords'>Arrowwords
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Sudoku Selection") !== false) {
                            echo "selected";
                        } ?> value='Sudoku Selection'>Sudoku Selection
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Best OF British") !== false) {
                            echo "selected";
                        } ?> value='Best OF British'>Best OF British
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Card Making and Papercraft") !== false) {
                            echo "selected";
                        } ?> value='Card Making and Papercraft'>Card Making and Papercraft
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Bella") !== false) {
                            echo "selected";
                        } ?> value='Bella'>Bella
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "That\'s Life") !== false) {
                            echo "selected";
                        } ?> value="That\'s Life">That's Life
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Chat") !== false) {
                            echo "selected";
                        } ?> value='Chat'>Chat
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Readers Digest") !== false) {
                            echo "selected";
                        } ?> value='Readers Digest'>Readers Digest
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Woman & Home") !== false) {
                            echo "selected";
                        } ?> value='Woman & Home'>Woman & Home
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Good Housekeeping") !== false) {
                            echo "selected";
                        } ?> value='Good Housekeeping'>Good Housekeeping
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Caravan Club Magazine") !== false) {
                            echo "selected";
                        } ?> value='Caravan Club Magazine'>Caravan Club Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Garden") !== false) {
                            echo "selected";
                        } ?> value='The Garden'>The Garden
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "CSMA Magazine") !== false) {
                            echo "selected";
                        } ?> value='CSMA Magazine'>CSMA Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "NFOP Magazine") !== false) {
                            echo "selected";
                        } ?> value='NFOP Magazine'>NFOP Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Eye to Eye Puzzles") !== false) {
                            echo "selected";
                        } ?> value='Eye to Eye Puzzles'>Eye to Eye Puzzles
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Motability Lifestyle") !== false) {
                            echo "selected";
                        } ?> value='Motability Lifestyle'>Motability Lifestyle
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Arthritis Digest") !== false) {
                            echo "selected";
                        } ?> value='Arthritis Digest'>Arthritis Digest
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Arthritis Today") !== false) {
                            echo "selected";
                        } ?> value='Arthritis Today'>Arthritis Today
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Stroke News") !== false) {
                            echo "selected";
                        } ?> value='Stroke News'>Stroke News
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "WI Life") !== false) {
                            echo "selected";
                        } ?> value='WI Life'>WI Life
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "National Trust Magazine") !== false) {
                            echo "selected";
                        } ?> value='National Trust Magazine'>National Trust Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "National Trust Scotland Magazine") !== false) {
                            echo "selected";
                        } ?> value='National Trust Scotland Magazine'>National Trust Scotland Magazine
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Nature\'s Home") !== false) {
                            echo "selected";
                        } ?> value="Nature\'s Home">Nature's Home
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Legion") !== false) {
                            echo "selected";
                        } ?> value='The Legion'>The Legion
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "The Legion Scotland") !== false) {
                            echo "selected";
                        } ?> value='The Legion Scotland'>The Legion Scotland
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "House Beautiful") !== false) {
                            echo "selected";
                        } ?> value='House Beautiful'>House Beautiful
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Candis") !== false) {
                            echo "selected";
                        } ?> value='Candis'>Candis
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Gardens Illustrated ") !== false) {
                            echo "selected";
                        } ?> value='Gardens Illustrated '>Gardens Illustrated
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Homes & Antiques") !== false) {
                            echo "selected";
                        } ?> value='Homes & Antiques'>Homes & Antiques
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Prima") !== false) {
                            echo "selected";
                        } ?> value='Prima'>Prima
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "OT Magazine") !== false) {
                            echo "selected";
                        } ?> value='OT Magazine'>OT Magazine
                    </option>
                </optgroup>
                <optgroup label="TV Sponsorship" disabled>
                    <option
                        <?php if (@strpos($values['a28'], "Channel itv3") !== false) {
                            echo "selected";
                        } ?> value='Channel itv3'>Channel itv3
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "ITV3 Morning") !== false) {
                            echo "selected";
                        } ?> value='ITV3 Morning'>ITV3 Morning
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "ITV3 Late Peak") !== false) {
                            echo "selected";
                        } ?> value='ITV3 Late Peak'>ITV3 Late Peak
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "Dickinson\'s Real Deal") !== false) {
                            echo "selected";
                        } ?> value="Dickinson\'s Real Deal">Dickinson's Real Deal
                    </option>
                    <option
                        <?php if (@strpos($values['a28'], "UKTV") !== false) {
                            echo "selected";
                        } ?> value='UKTV'>UKTV
                    </option>
                </optgroup>
            </select>
        </div>

        <div class="question">
            <label>Does the customer have a media reference number?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a26][]" id="optionsRadios1" data-show-notes="true"
                           value="Yes" <?php if (@strpos($values['a26'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a26][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a26'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>Please enter the media reference number</label>
                <br>
                <input type="text" name="answers[a27]" value="<?php echo @$values['a27'] ?>" class="form-control"
                       placeholder="Add Media Reference Number if applicable"/>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div style="border-bottom: 1px solid grey; margin-top: 10px; margin-bottom: 20px; font-weight: bold">
                    CUSTOMER 1
                </div>

                <input id="contact-id" name="answers[a1]" value="<?php echo @$values['a1'] ?>" type="hidden"/>
                <input id="contact-fullname" name="answers[a2]" value="<?php echo @$values['a2'] ?>" type="hidden"/>

                <div class="form-group">
                    <label>Customer Name</label>
                    <br>
                    <select id="contact-name">
                        <?php foreach ($contacts as $contact) { ?>
                            <option <?php if ($contact['contact_id'] == @$values['a1']) {
                                echo "selected";
                            } ?> value="<?php echo $contact['contact_id'] ?>"><?php echo $contact['name'] ?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $("#contact-name").selectpicker();
                            $(document).on('change', '#contact-name', function () {
                                $("#contact-id").val($(this).val());
                                $('#contact-fullname').val($("#contact-name option:selected").text());
                            });
                            $("#contact-name").trigger("change");
                        });
                    </script>
                </div>
                <div class="form-group relative">
                    <label>Customer age?</label>
                    <br>
                    <input type="text" name="answers[a3]" class="form-control" placeholder="Enter the age in years"
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
                <div class="form-group">
                    <label>Customer height?</label>
                    <br>
                    <input type="text" name="answers[a4]" class="form-control" style="width:50px; display:inline-block"
                           value="<?php echo @$values['a4'] ?>"/> <span>Feet</span>
                    <input type="text" name="answers[a5]" class="form-control" style="width:50px;display:inline-block"
                           value="<?php echo @$values['a5'] ?>"/> <span>Inches</span>
                </div>

                <div class="form-group">
                    <label>Please enter the customer build</label>
                    <br>
                    <select name="answers[a29]" class="selectpicker" data-width="100%"
                            data-size="5">
                        <option value=''></option>
                        <option <?php if (@strpos($values['a29'], "Small") !== false) {
                            echo "selected";
                        } ?> value='Small'>Small
                        </option>
                        <option <?php if (@strpos($values['a29'], "Medium") !== false) {
                            echo "selected";
                        } ?> value='Medium'>Medium
                        </option>
                        <option <?php if (@strpos($values['a29'], "Large") !== false) {
                            echo "selected";
                        } ?> value='Large'>Large
                        </option>
                    </select>
                </div>

                <div class="question">
                    <label>Can the customer walk unaided?</label><br>

                    <div class="radio" style="display:inline-block">
                        <label>
                            <input type="radio" name="answers[a9][]" id="optionsRadios1"
                                   value="Yes" <?php if (@strpos($values['a9'], "Yes") !== false) {
                                echo "checked";
                            } ?> />
                            Yes </label>
                    </div>
                    <div class="radio" style="display:inline-block; margin-left:20px">
                        <label>
                            <input type="radio" name="answers[a9][]" id="optionsRadios2" data-show-notes="true"
                                   value="No" <?php if (@strpos($values['a9'], "No") !== false) {
                                echo "checked";
                            } ?>>
                            No </label>
                    </div>
                    <div class="form-group">
                        <label>Enter notes here</label>
                        <input type="text" name="answers[a10]" class="form-control"
                               placeholder="Enter notes if they answered no above " value="<?php echo @$values['a10'] ?>"/>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">

                <div style="border-bottom: 1px solid grey; margin-top: 10px; margin-bottom: 20px; font-weight: bold">
                    CUSTOMER 2
                </div>

                <input id="contact2-id" name="answers[a30]" value="<?php echo @$values['a30'] ?>" type="hidden"/>
                <input id="contact2-fullname" name="answers[a31]" value="<?php echo @$values['a31'] ?>" type="hidden"/>

                <div class="form-group">
                    <label>Customer Name</label>
                    <br>
                    <select id="contact2-name">
                        <option value=""></option>
                        <?php foreach ($contacts as $contact) { ?>
                            <option <?php if ($contact['contact_id'] == @$values['a30']) {
                                echo "selected";
                            } ?> value="<?php echo $contact['contact_id'] ?>"><?php echo $contact['name'] ?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $("#contact2-name").selectpicker();
                            $(document).on('change', '#contact2-name', function () {
                                $("#contact2-id").val($(this).val());
                                $('#contact2-fullname').val($("#contact2-name option:selected").text());
                            });
                            $("#contact2-name").trigger("change");
                        });
                    </script>
                </div>
                <div class="form-group relative">
                    <label>Customer age?</label>
                    <br>
                    <input type="text" name="answers[a32]" class="form-control" placeholder="Enter the age in years"
                           value="<?php echo @$values['a32'] ?>"/>
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
                <div class="form-group">
                    <label>Customer height?</label>
                    <br>
                    <input type="text" name="answers[a33]" class="form-control" style="width:50px; display:inline-block"
                           value="<?php echo @$values['a33'] ?>"/> <span>Feet</span>
                    <input type="text" name="answers[a34]" class="form-control" style="width:50px;display:inline-block"
                           value="<?php echo @$values['a34'] ?>"/> <span>Inches</span>
                </div>

                <div class="form-group">
                    <label>Please enter the customer build</label>
                    <br>
                    <select name="answers[a35]" class="selectpicker" data-width="100%"
                            data-size="5">
                        <option value=''></option>
                        <option <?php if (@strpos($values['a35'], "Small") !== false) {
                            echo "selected";
                        } ?> value='Small'>Small
                        </option>
                        <option <?php if (@strpos($values['a35'], "Medium") !== false) {
                            echo "selected";
                        } ?> value='Medium'>Medium
                        </option>
                        <option <?php if (@strpos($values['a35'], "Large") !== false) {
                            echo "selected";
                        } ?> value='Large'>Large
                        </option>
                    </select>
                </div>

                <div class="question_opt">
                    <label>Can the customer walk unaided?</label><br>

                    <div class="radio" style="display:inline-block">
                        <label>
                            <input type="radio" name="answers[a36][]" id="optionsRadios1"
                                   value="Yes" <?php if (@strpos($values['a36'], "Yes") !== false) {
                                echo "checked";
                            } ?> />
                            Yes </label>
                    </div>
                    <div class="radio" style="display:inline-block; margin-left:20px">
                        <label>
                            <input type="radio" name="answers[a36][]" id="optionsRadios2" data-show-notes="true"
                                   value="No" <?php if (@strpos($values['a36'], "No") !== false) {
                                echo "checked";
                            } ?>>
                            No </label>
                    </div>
                    <div class="form-group">
                        <label>Enter notes here</label>
                        <input type="text" name="answers[a37]" class="form-control"
                               placeholder="Enter notes if they answered no above " value="<?php echo @$values['a37'] ?>"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-12" style="border-bottom: 1px solid grey; margin-top: 10px; margin-bottom: 20px; font-weight: bold"></div>
        </div>


        <br>

        <div class="form-group">
            <label>Reason home consultation required?</label>
            <br>
            <input type="text" name="answers[a6]" class="form-control"
                   placeholder="Enter the reason for the home consultation" value="<?php echo @$values['a6'] ?>"/>
        </div>
        <div class="question">
            <label>Does customer need assistance to stand/transfer independently?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a7][]" id="optionsRadios1" data-show-notes="true"
                           value="Yes" <?php if (@strpos($values['a7'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input class="q3-question helper-required" type="radio" name="answers[a7][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a7'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>Enter notes here</label>
                <input type="text" name="answers[a8]" class="form-control"
                       placeholder="Enter notes if they answered yes above " value="<?php echo @$values['a8'] ?>"/>
            </div>
        </div>
        <div class="question">
            <label>Does Customer have any problems with sight/hearing/speech/memory?</label><br>

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
                    <input type="radio" name="answers[a11][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a11'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>Enter notes here</label>
                <input type="text" name="answers[a12]" value="<?php echo @$values['a12'] ?>" class="form-control"
                       placeholder="Enter notes if they answered yes above "/>
            </div>
        </div>
        <div class="question">
            <label>Can anyone familiar with the customers needs also be available during the Home
                Consultation?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a13][]" id="optionsRadios1" data-show-notes="true"
                           value="Yes" <?php if (@strpos($values['a13'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a13][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a13'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>Please capture the name of the helper/carer if applicable</label>
                <br>
                <input type="text" name="answers[a14]" value="<?php echo @$values['a14'] ?>" class="form-control"
                       placeholder="Helper/carers persons name"/>
            </div>
        </div>

        <div class="question">
            <label>Are there any vehicle or parking restrictions at the Customer's Accommodation?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a15][]" id="optionsRadios1" data-show-notes="true"
                           value="Yes" <?php if (@strpos($values['a15'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a15][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a15'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>Please enter notes</label>
                <br>
                <input type="text" name="answers[a16]" value="<?php echo @$values['a16'] ?>" class="form-control"
                       placeholder="Add parking details if applicable"/>
            </div>
        </div>
        <div class="question">
            <label>Are there any issues with stairs/doorways at the Customer's Accommodation?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a17][]" id="optionsRadios1" data-show-notes="true"
                           value="Yes" <?php if (@strpos($values['a17'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a17][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a17'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>Please enter notes</label>
                <br>
                <input type="text" name="answers[a18]" value="<?php echo @$values['a18'] ?>" class="form-control"
                       placeholder="Enter access notes here if applicable"/>
            </div>
        </div>
        <div class="question">
            <label>Confirm there is a power supply and space to set demonstrate and demonstrate the
                chair(s)?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a19][]" id="optionsRadios1"
                           value="Yes" <?php if (@strpos($values['a19'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a19][]" id="optionsRadios2" data-show-notes="true"
                           value="No" <?php if (@strpos($values['a19'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
            <div class="form-group">
                <label>Please enter alternate arrangements</label>
                <br>
                <input type="text" name="answers[a20]" value="<?php echo @$values['a20'] ?>" class="form-control"
                       placeholder="Enter access power supply notes here if applicable"/>
            </div>
        </div>
        <div class="question">
            <label>Confirm that the Home Consultation will take approximately 1 hour</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a21][]" id="optionsRadios1" data-show-notes="true"
                           value="Yes" <?php if (@strpos($values['a21'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a21][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a21'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
        </div>
        <div>
            <label>Confirm the home consultant will telephone them the day before the consultation and if known, provide
                the names of the home consultant and driver?</label><br>

            <div class="radio" style="display:inline-block">
                <label>
                    <input type="radio" name="answers[a22][]" id="optionsRadios1"
                           value="Yes" <?php if (@strpos($values['a22'], "Yes") !== false) {
                        echo "checked";
                    } ?> />
                    Yes </label>
            </div>
            <div class="radio" style="display:inline-block; margin-left:20px">
                <label>
                    <input type="radio" name="answers[a22][]" id="optionsRadios2"
                           value="No" <?php if (@strpos($values['a22'], "No") !== false) {
                        echo "checked";
                    } ?>>
                    No </label>
            </div>
        </div>

        <div class="form-group">
            <label>Any other information relevant</label>
            <br>
            <textarea class="form-control" style="height:50px"
                      name="answers[a23][]"><?php echo @$values['a23'] ?></textarea>
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
       <div id="page-success" class="alert alert-success hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-info"  class=" alert alert-info hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-warning"  class=" alert alert-warning hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-danger"  class=" alert alert-danger hidden alert-dismissable"><span class="alert-text"></span><span
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
        $('[data-toggle="tooltip"]').tooltip()
        //hide all the notes

        $.each($('.question, .question_opt'), function () {
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
                $(this).closest('.question, .question_opt').find('.form-group').show();
            } else {
                $(this).closest('.question, .question_opt').find('.form-group').hide().find('input').val('');
            }

            $.ajax({
                type: "POST",
                data: $('#form').serialize() + '&save=1',
				dataType: "JSON"
            }).done(function(response){
				$('input[name="id"]').val(response.id);
			});
        });
        $(document).on('click', '#save-form', function (e) {
            e.preventDefault();
            if (check_form()) {
                $.ajax({
                    type: "POST",
                    data: $('#form').serialize() + '&save=1',
					dataType:"JSON",
                }).done(function (response) {
                    flashalert.success("Form was saved");
					$('input[name="id"]').val(response.id);
                }).fail(function(){
					flashalert.danger("There was an error saving the form");
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
                } else {
					$(this).removeAttr('style');
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

        //Sub Hear Disable/Enable
        //Enable just which have the main option selected
        $(this).find(":selected").each(function () {
            var selected_value = $(this).val();
            var optgroup = $("#sub-hear").find("optgroup[label='" + selected_value + "']");
            optgroup.attr('disabled', false)
        });
        $("#sub-hear").selectpicker('refresh');

        //On change the first option on the hear question
        $('#hear').on('change', function (e) {

            //First of all disable all of them
            $("#sub-hear").find("optgroup").each(function () {
                $(this).attr('disabled', true)
            });

            //Enable just which have the main option selected
            $(this).find(":selected").each(function () {
                var selected_value = $(this).val();
                var optgroup = $("#sub-hear").find("optgroup[label='" + selected_value + "']");
                optgroup.attr('disabled', false)
            });

            //Unselect which are disabled
            $("#sub-hear").find("optgroup[disabled]").each(function () {
                $(this).find(":selected").attr("selected", false);
            });

            $("#sub-hear").selectpicker('refresh');
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