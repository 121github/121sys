<div class="custom-exports">
    <ul id="tabs" class="nav nav-tabs" role="tablist">
        <li class="active"><a role="tab" data-toggle="tab" href="#query-tab">Query</a></li>
        <li><a role="tab" data-toggle="tab" href="#graphs-tab"> Graphs</a></li>
        <li><a role="tab" data-toggle="tab" href="#export-tab"> Export</a></li>
        <li><a role="tab" data-toggle="tab" href="#viewers-tab"> Viewers</a></li>
    </ul>
    <form name="edit-export-form" method="post" class="export-form edit-export-form">
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="query-tab">
                <input type="hidden" name="export_forms_id">
                <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold">DETAILS</div>
                <div class="row">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-sm" name="name"
                                       placeholder="Enter the name" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2">Description</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-sm" name="description"
                                       placeholder="Enter the description"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2">Header</label>
                            <div class="col-sm-10">
                                <textarea rows="3" class="form-control" name="header" placeholder="Enter the header separated by ;" required></textarea>
                            </div>
                        </div>
                        <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold"> QUERY <span
                                class="pull-right"> <a role="button" class="collapsed" data-toggle="collapse"
                                                       href="#collapsePreview"> Preview </a> </span></div>
                        <div class="col-lg-12">
                            <div class="row collapse" id="collapsePreview">
                                <div class="preview-qry small"
                                     style="background-color: #EBE2E2; padding: 10px; margin-bottom: 5px"></div>
                            </div>
                        </div>

                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2">Select</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" cols="115" rows="3" name="query" placeholder="Enter the query" required></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2">Order By</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="order_by"
                                           placeholder="Enter the field if you need to order by"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2">Group By</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="group_by"
                                           placeholder="Enter the field if you need to group by"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold">FITLERS</div>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group input-group-sm">
                                <p>Date Filter</p>
                                <input type="text" class="form-control" name="date_filter"
                                       placeholder="Eg. date_added"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group input-group-sm">
                                <p>Campaign Filter</p>
                                <input type="text" class="form-control" name="campaign_filter"
                                       placeholder="Eg. campaign_id"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group input-group-sm">
                                <p>Outcome Filter</p>
                                <input type="text" class="form-control" name="outcome_filter"
                                       placeholder="Eg. outcome_id"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group input-group-sm">
                                <p>Source Filter</p>
                                <input type="text" class="form-control" name="source_filter"
                                       placeholder="Eg. source_id"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group input-group-sm">
                                <p>Pot Filter</p>
                                <input type="text" class="form-control" name="pot_filter"
                                       placeholder="Eg. pot_id"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group input-group-sm">
                                <p>Team Filter</p>
                                <input type="text" class="form-control" name="team_filter"
                                       placeholder="Eg. team_id"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group input-group-sm">
                                <p>Branch Filter</p>
                                <input type="text" class="form-control" name="branch_filter"
                                       placeholder="Eg. branch_id"/>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group input-group-sm">
                                <p>User Filter</p>
                                <input type="text" class="form-control" name="user_filter"
                                       placeholder="Eg. user_id"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="graphs-tab">
                <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold"> GRAPHS <span
                        class="pull-right"> <a role="button" class="collapsed" data-toggle="collapse"
                                               href="#collapseGraph"> Add new </a> </span></div>
                <div class="collapse" id="collapseGraph" style="margin-top: 20px">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default active">
                                    <input type="radio" value="bars" checked="checked" aria-label="..."
                                           name="graph_type">
                                    Bars Chart <span class="fa fa-bar-chart"></span> </label>
                                <label class="btn btn-default">
                                    <input type="radio" value="pie" aria-label="..." name="graph_type">
                                    Pie Chart <span class="fa fa-pie-chart"></span> </label>
                                <label class="btn btn-default">
                                    <input type="radio" value="line" aria-label="..." name="graph_type">
                                    Line Chart <span class="fa fa-line-chart"></span> </label>
                                <label class="btn btn-default">
                                    <input type="radio" value="area" aria-label="..." name="graph_type">
                                    Line Chart <span class="fa fa-area-chart"></span> </label>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px">
                        <div class="col-lg-3">
                            <div class="btn-group">
                                <div class="form-group input-group-sm">
                                    <p>Name (*)</p>
                                    <input type="text" class="form-control" name="graph_name"
                                           placeholder="Enter the name for the graph"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="btn-group">
                                <div class="form-group input-group-sm">
                                    <p>X Axis (*)</p>
                                    <input type="text" class="form-control" name="x_value"
                                           placeholder="Enter the x axis"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="btn-group">
                                <div class="form-group input-group-sm">
                                    <p>Y Axis</p>
                                    <input type="text" class="form-control" name="y_value"
                                           placeholder="Enter the y axis"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="btn-group">
                                <div class="form-group input-group-sm">
                                    <p>Z Axis</p>
                                    <input type="text" class="form-control" name="z_value"
                                           placeholder="Enter the z axis"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12"><span
                                class="btn btn-success btn-xs save-export-graph-btn pull-right"> <span
                                    class="fa fa-save"></span> Save </span></div>
                    </div>
                    <div
                        style="border-bottom: 1px solid grey; margin-top: 10px; margin-bottom: 20px; font-weight: bold"></div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="export-graph-list"> No graphs added</div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="export-tab">
                <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold">USERS</div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="btn-group">
                            <div class="form-group input-group-sm">
                                <p>Select the users that will recieve this data by email</p>
                                <select id="user_select" class="selectpicker user_select" data-size="7"
                                        name="user_id[]" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4"></div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="viewers-tab">
                <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold">VIEWERS</div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="btn-group">
                            <div class="form-group input-group-sm">
                                <p>Select the users that could see the report panels</p>
                                <select id="viewers_select" class="selectpicker viewers_select" data-size="7"
                                        name="viewer_id[]" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4"></div>
                </div>
            </div>
    </form>
</div>
