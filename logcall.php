<?php
	require_once "db.php";
	$conn = new mysqli(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);
	$sql = "SELECT * FROM incident_type";
	$result = $conn->query($sql);
	$incidentTypes = [];
	while($row = $result->fetch_assoc()){
		$id = $row["incident_type_id"];
		$type = $row["incident_type_description"];
		$incidentType = ["id"=>$id, "type"=>$type];
		array_push($incidentTypes,$incidentType);
	}
	$conn->close();
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logcall</title>
<!-- <link rel="stylesheet" href="css/bootstrap-4.3.1.css"> -->
<link href="css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="container" style="width:900px">
	
	<?php
		include "header.php";
	?>
	
  <section class="mt-3">
    <form action="dispatch.php" method="post">
		
      <div class="form-group row">
        <label for="callerName" class="col-sm-4 col-form-lable">Caller's Name</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="callerName" name="callerName" required>
        </div>
      </div>
		
		<div class="form-group row">
        <label for="contactNumber" class="col-sm-4 col-form-lable">Contact Number</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="contactNumber" name="contactNumber" required>
        </div>
      </div>
		
		<div class="form-group row">
        <label for="locationOfIncident" class="col-sm-4 col-form-lable">Location Of Incident</label>
        <div class="col-sm-8">
          <input type="text" class="form-control" id="locationOfIncident" name="locationOfIncident" required>
        </div>
      </div>
		
		<div class="form-group row">
        <label for="typeOfIncident" class="col-sm-4 col-form-lable">Type Of Incident</label>
        <div class="col-sm-8">
          <select id="typeOfIncident" class="form-control" name="typeOfIncident" required>
			<option value="">Select</option>
			  
			  <?php
			  	foreach($incidentTypes as $incidentType){
					echo "<option value=\"" . $incidentType["id"] . "\">" . $incidentType[type] . "</option>";
				}
			  ?>
			  
			<option value="accident">Car Accident</option>
			</select>
        </div>
      </div>
		
		<div class="form-group row">
        <label for="descriptionOfIncident" class="col-sm-4 col-form-lable">Description Of Incident</label>
        <div class="col-sm-8">
          <textarea name="descriptionOfIncident" class="form-control" rows="5" id="descriptionOfIncident" required></textarea>
        </div>
      </div>
		
		<div class="form-group row">
        <div class="offset-sm-4 col-sm-8">
        <button type="submit" class="btn btn-primary" name="proccessCallBn" id="submit">Proccess Call</button>
			
        </div>
      </div>
		<!-- Extra(if type enter on Description of incident , will click on Proccess call button) -->
		<script>
			var input = document.getElementById("descriptionOfIncident");
			input.addEventListener("keyup", function(event) {
  			if (event.keyCode === 13) {
   			event.preventDefault();
   			document.getElementById("submit").click();}
			});
		</script>
      
    </form>
  </section>
	
	<?php
		include "footer.php";
	?>
	
</div>
<script src="js/jquery-3.4.1.min.js"></script> 
<script src="js/popper.min.js"></script> 
<script src="js/bootstrap-4.4.1.js"></script>
</body>
</html>
