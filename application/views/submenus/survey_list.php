<div class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
        <p class="navbar-text" style="color:#fff; font-weight:700"><?php echo $title ?></p>
    </ul>
    <?php if (!isset($hide_filter)) { ?>
        <ul class="nav navbar-nav pull-right" id="submenu-filters">
            <li>
                <div class="navbar-btn">
                    <div class="btn-group">
                        <a type="button" class="btn btn-default btn" data-modal="choose-columns"
                           data-table-id="5"><span
                                class="fa fa-table"></span> Views
                        </a>
                    </div>
                    <?php if ($global_filter) {
                        $filter_class = "btn-default"; ?>
                        <?php if (isset($_SESSION['filter']['values'])) { ?>
                            <? if (@array_key_exists("pot_id", $_SESSION['filter']['values']) || @array_key_exists("source_id", $_SESSION['filter']['values']) || @array_key_exists("outcome_id", $_SESSION['filter']['values']) || @array_key_exists("postcode", $_SESSION['filter']['values'])) {

                                $filter_class = "btn-success";
                            } ?>
                        <?php } ?>
                        <div class="btn-group">
                            <a href="#global-filter" id="submenu-filter-btn"
                               class="btn <?php echo $filter_class ?>"><span
                                    class="fa fa-filter"></span> Filter
                            </a>

                        </div>
                    <?php } ?>
                    <div class="btn-group desktop-only">
                        <input
                            value="<?php echo isset($_SESSION['filter']['values']['postcode']) ? $_SESSION['filter']['values']['postcode'] : "" ?>"
                            name="postcode" class="form-control" style="width:130px" placeholder="Enter Postcode"/>
                    </div>
                    <div class="desktop-only">
                        <select name="distance" data-width="130" class="selectpicker">
                        <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "9999") {
                                echo "selected";
                            } ?> value="9999">Any Distance</option>
                            <option value="">Match Postcode</option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "1") {
                                echo "selected";
                            } ?> value="1">1 Mile
                            </option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "3") {
                                echo "selected";
                            } ?> value="3">3 Miles
                            </option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "5") {
                                echo "selected";
                            } ?> value="5">5 Miles
                            </option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "10") {
                                echo "selected";
                            } ?> value="10">10 Miles
                            </option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "20") {
                                echo "selected";
                            } ?> value="20">20 Miles
                            </option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "30") {
                                echo "selected";
                            } ?> value="30">30 Miles
                            </option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "50") {
                                echo "selected";
                            } ?> value="50">50 Miles
                            </option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "75") {
                                echo "selected";
                            } ?> value="75">75 Miles
                            </option>
                            <option <?php if (isset($_SESSION['filter']['values']['distance']) && $_SESSION['filter']['values']['distance'] == "100") {
                                echo "selected";
                            } ?> value="100">100 Miles
                            </option>
                        </select>
                    </div>
                    <div class="desktop-only">
                        <button class="btn btn-primary" id="submenu-filter-submit">Go</button>
                    </div>
                </div>
            </li>

        </ul>
    <?php } ?>
</div>

<script>
    $(document).ready(function () {
        $('#submenu-filter-submit').on('click', function (e) {
            e.preventDefault();
            var postcode = $('#submenu-filters input[name="postcode"]').val();
            var distance = $('#submenu-filters select[name="distance"]').val();
            $('#global-filter-form').find('input[name="postcode"]').val(postcode);
            $('#global-filter-form').find('select[name="distance"]').val(distance).selectpicker('refresh');
            //submit the filter
            $('#global-filter-form').find('.apply-filter').trigger('click');
        });
    });

</script>