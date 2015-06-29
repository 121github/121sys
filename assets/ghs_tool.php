<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GHS Tool</title>

    <script src="https://www.121system.com/assets/js/lib/jquery.min.js"></script>
    <script src="https://www.121system.com/assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
   <link rel="stylesheet" href="https://www.121system.com/assets/css/bootstrap.css">
       <script src="https://www.121system.com/assets/js/main.js"></script>
</head>

<body>
<div class="row">
<div class="col-lg-12">
<form id="form">
<div class="form-group">
<select class="selectpicker" id="function">
<option value="trackvia/review_required">Prequal</option>
</select>
</div>
<div class="form-group">
<label>URN</label>
<input class="form-control" id="urnval" name="urn"/>
</div>
<div class="form-group">
<button class="btn btn-primary" id="update">Update</button>
</div>
</form>
</div>
<div class="col-lg-12">

<div id="response"></div>

</div>
</div>
</body>
<script>
$('#update').click(function(e){
	e.preventDefault();
	$.ajax({
		url:"https://www.121system.com/"+$('#function').val(),
	type:"POST",
	dataType:"JSON",
	data:{ urn:$('#urnval').val() }
	}).done(function(response){
		$('#response').html(response);
	});
});

</script>

</html>