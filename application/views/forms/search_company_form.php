<div class="search-container">
<ul class="nav nav-tabs" style=" background:#eee; width:100%;">
    <li class="active"><a href="#cosearch" class="tab" data-toggle="tab">Search</a></li>
    <li class="searchresult-tab"><a href="#cosearchresult" class="tab" data-toggle="tab">Results <span class="num-results">0</span></a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="cosearch">
         <p class="text-info"><span class="glyphicon glyphicon-question-sign"></span> Enter a company name and/or company number to search the Companies House database for details</p>
        <form class="form-horizontal search-company-form">
            <input name="urn" type="hidden" value="">
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

        </form>
    </div>
    <div class="tab-pane" id="cosearchresult">
    <p class="text-info"><span class="glyphicon glyphicon-info-sign"></span> This is live data from Companies House<br /></p>
        <div class="table-container">
            <table class="table table-hover search-table">
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
</div>