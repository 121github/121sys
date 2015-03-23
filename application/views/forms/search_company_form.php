<ul class="nav nav-tabs" style=" background:#eee; width:100%;">
    <li class="active"><a href="#cosearch" class="tab" data-toggle="tab">Search</a></li>
    <li class="searchresult-tab"><a href="#cosearchresult" class="tab" data-toggle="tab">Results <span class="num-results">0</span></a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="cosearch">
        <a href="https://www.gov.uk/government/organisations/companies-house" target="_blank" ><img style="margin-bottom: 5px;" height="40px;" src="<?php echo base_url(); ?>assets/img/companieshouse.png"></a>
        <form class="form-horizontal search-company-form">
            <input name="urn" type="hidden" value="<?php echo $urn ?>">
            <input name="company_id" type="hidden" value="">

            <div class="form-group input-group-sm">
                <label class="col-sm-3 control-label">Co. Name</label>

                <div class="col-sm-9">
                    <input type="text" class="form-control" placeholder="Company name" name="name" value="">
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-3 control-label">Co. Number</label>

                <div class="col-sm-9">
                    <input name="conumber" placeholder="Company number" type='text' class="form-control" value=""/>
                </div>
            </div>
            <div class="form-actions pull-right">
                <span class="btn btn-primary search-company-action">Search</span>
                <span class="btn btn-default close-company-btn">Close</span>
            </div>
        </form>
    </div>
    <div class="tab-pane" id="cosearchresult">
        <a href="https://www.gov.uk/government/organisations/companies-house" target="_blank" ><img style="margin-bottom: 5px;" height="40px;" src="<?php echo base_url(); ?>assets/img/companieshouse.png"></a>
        <div class="table-container">
            <table class="table search-table">
                <thead>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Status</th>
                    <th>Date</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="result-pagination">
            </div>
        </div>
    </div>
</div>
