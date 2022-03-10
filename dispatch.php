<?php
require_once "db.php";
	$conn = new mysqli(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);
	$sql = "SELECT patrolcar.patrolcar_id,patrolcar_status.patrolcar_status_description FROM patrolcar INNER JOIN patrolcar_status ON patrolcar.patrolcar_status_id = patrolcar_status.patrolcar_status_id;";
	$result = $conn->query($sql);

	$callerName = $_POST["callerName"];
	$contactNumber = $_POST["contactNumber"];
	$locationOfIncident = $_POST["locationOfIncident"];
	$typeOfIncident = $_POST["typeOfIncident"];
	$descriptionOfIncident = $_POST["descriptionOfIncident"];

	// for dispatch car options
	$cars = [];
	while($row = $result->fetch_assoc()){
		$id = $row["patrolcar_id"];
		$status = $row["patrolcar_status_description"];
		$car = ["id"=>$id, "status"=>$status];
		array_push($cars,$car);
	}
	// $conn->close();

	$proccessCallBnClicked = isset($_POST["proccessCallBn"]);
	$dispatchBnClicked = isset($_POST["dispatchBn"]);

	// if did not press process call button on logcall, cannot move to dispatch.php
	if($dispatchBnClicked == false && $proccessCallBnClicked == false) {
		header("location: logcall.php");
	}

//	Sample Enchancement
$sql = "SELECT incident.caller_name, incident.phone_number, incident_type.incident_type_description, incident.incident_location, incident.incident_description, incident.time_called, incident_status.incident_status_description from incident inner join incident_type on incident.incident_type_id = incident_type.incident_type_id inner join incident_status on incident.incident_status_id = incident_status.incident_status_id where incident.incident_description like " . "'%" . $descriptionOfIncident . "%'" ;

$result = $conn->query($sql);
$incidents = [];
while($row = $result->fetch_assoc()){
	$caller_name = $row["caller_name"];
	$phone_number = $row["phone_number"];
	$incident_type = $row["incident_type_description"];
	$location = $row["incident_location"];
	$incident_description = $row["incident_description"];
	$time_called = $row["time_called"];
	$status = $row["incident_status_description"];
	
	$incident = ["caller_name"=>$caller_name, "phone_number"=>$phone_number, "incident_type"=>$incident_type, "location"=>$location, "incident_description"=>$incident_description, "time_called"=>$time_called, "status"=>$status];
	array_push($incidents, $incident);
}
// end of sample enchancement
		
		// ...
		if($dispatchBnClicked == true) {
		$insertIncidentSuccess = false;
		$hasCarSelection = isset($_POST["cbCarSelection"]);
		$patrolcarDispatched = [];
		$numOfPatrolCarDispatched = 0;
		if($hasCarSelection == true) {
			$patrolcarDispatched = $_POST["cbCarSelection"];
			$numOfPatrolCarDispatched = count($patrolcarDispatched);
		}
		$numOfPatrolCarDispatched = 0;
		$incidentStatus = 0;
		
		if($numOfPatrolCarDispatched > 0){
			$incidentStatus = 2; // Dispatched
		}
		else{
			$incidentStatus = 1; // Pending
		}

		// inserting data from logcall to database
		$sql = "INSERT INTO `incident` (`caller_name`,`phone_number`,`incident_type_id`,`incident_location`,`incident_description`,`incident_status_id`,`time_called`) VALUE ('" . $callerName . "','" . $contactNumber . "','" . $typeOfIncident . "','" . $locationOfIncident . "','" . $descriptionOfIncident . "','" . $incidentStatus ."',now());";
		echo "Error: 1 " . $sql . "<br>" . $conn->error;
		
		$conn = new mysqli(DB_SERVER,DB_USER,DB_PASSWORD,DB_DATABASE);
		$insertIncidentSuccess = $conn->query($sql);
		if($insertIncidentSuccess == false) {
			echo "Error: 2 " . $sql . "<br>" . $conn->error;
		}
		
		// echo incident id
		$incidentId = mysqli_insert_id($conn);
		echo "<br> new incident id: " . $incidentId;
		
		$updateSuccess = false;
		$insertDispatchSuccess = false;
		
		// giving extra names
		foreach($patrolcarDispatched as $eachCarId) {
			 echo $eachCarId . "<br>";
			
			// Update database patrolcar status to dispatched
			$sql = "UPDATE `patrolcar` SET `patrolcar_status_id`='1' WHERE `patrolcar_id`='" . $eachCarId ."'";
			$updateSuccess = $conn->query($sql);
			if($updateSuccess == false) {
				echo "Error: 3 " . $sql . "<br>" . $conn->error;
			}
			
			// insert patrol car dispatched info data
			$sql = "INSERT INTO `dispatch`(`incident_id`, `patrolcar_id`, `time_dispatched`) VALUES (" . $incidentId . ",'" . $eachCarId . "',now())";
			$insertDispatchSuccess = $conn->query($sql);
			if($insertDispatchSuccess == false) {
				echo "Error: 4 " . $sql . "<br>" . $conn->error;
			}
			
		}
		$conn->close();
		
		// return to logcall page after dispatch success
		if($insertDispatchSuccess == true && $updateSuccess == true && $insertIncidentSuccess == true) {
			header("location: logcall.php");
		}
	}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dispatch</title>
<!-- <link rel="stylesheet" href="css/bootstrap-4.3.1.css"> -->
<link href="css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="container" style="width:900px">
  
	<?php
		include "header.php";
	?>
	
  <section class="mt-3">
    <form action="<?php echo htmlentities($_SERVER["PHP_SELF"])?>" method="post">
		
      <div class="form-group row">
         <label for="callerName" class="col-sm-4 col-form-lable">Caller's Name</label>
        <div class="col-sm-8">
			<span>
				<?php
					echo $callerName;
				?>
				<input type="hidden" id="callerName" name="callerName" value="<?php echo $callerName; ?>">
			</span>
        </div>
      </div>
		
		<div class="form-group row">
        <label for="contactNumber" class="col-sm-4 col-form-lable">Contact Number</label>
        <div class="col-sm-8">
			<span>
				<?php
					echo $contactNumber;
				?>
				<input type="hidden" id="contactNumber" name="contactNumber" value="<?php echo $contactNumber; ?>">
			</span>
        </div>
      </div>
		
		<div class="form-group row">
        <label for="locationOfIncident" class="col-sm-4 col-form-lable">Location Of Incident</label>
        <div class="col-sm-8">
			<span>
				<?php
					echo $locationOfIncident;
				?>
				 <input type="hidden" id="locationOfIncident" name="locationOfIncident" value="<?php echo $locationOfIncident; ?>">
			</span>
        </div>
      </div>
		
		<div class="form-group row">
        <label for="typeOfIncident" class="col-sm-4 col-form-lable">Type Of Incident</label>
        <div class="col-sm-8">
			<span>
				<?php
					echo $typeOfIncident;
				?>
				<input id="typeOfIncident" type="hidden" name="typeOfIncident" value="<?php echo $typeOfIncident; ?>">
			</span>
        </div>
      </div>
		
		<div class="form-group row">
        <label for="descriptionOfIncident" class="col-sm-4 col-form-lable">Description Of Incident</label>
        <div class="col-sm-8">
			<span>
				<?php
					echo $descriptionOfIncident;
				?>
				<input type="hidden" name="descriptionOfIncident" id="descriptionOfIncident" value="<?php echo $descriptionOfIncident; ?>">
			</span>
        </div>
      </div>
		
		<br>
		
<!-- sample enchancement -->
		<div>
			<lable for="patrol car" class="col-sm-4 col-form-label">Similar Incident(s)</lable>
			<div class="col-sm-8">
				<table class="table table-striped">
					<tbody>
						<tr>
							<th scope="col">Caller Name</th>
							<th scope="col">Caller Number</th>
							<th scope="col">Incident Type</th>
							<th scope="col">Incident Location</th>
							<th scope="col">Incident Description</th>
							<th scope="col">Incident Status</th>
							<th scope="col">Time Called</th>
						</tr>
						<?php
							foreach($incidents as $incident) {
								echo "<tr>" .
									"<td>" . $incident["caller_name"] . "</td>" . 
									"<td>" . $incident["phone_number"] . "</td>" .
									"<td>" . $incident["incident_type"] . "</td>" .
									"<td>" . $incident["location"] . "</td>" .
									"<td>" . $incident["incident_description"] . "</td>" .
									"<td>" . $incident["status"] . "</td>" .
									"<td>" . $incident["time_called"] . "</td>" .
									"</tr>";
							};
						?>
					</tbody>
				</table>
			</div>
		</div>
<!-- end of sample enchancement -->
		
		<br>
		
		<div class="form-group row">
        <label for="patrolCar" class="col-sm-4 col-form-lable">Choose Patrol Car(s)</label>
        <div class="col-sm-8">
			<table class="table table-striped">
				<tbody>
					<tr>
						<th>Car Number</th>
						<th>Status</th><br>
						<th></th>
					</tr>
					<?php
						foreach($cars as $car){
							echo "<tr>" .
									"<td>" . $car["id"] . "</td>" .
									"<td>" . $car["status"] . "</td>" .
									"<td>" .
										"<input type=\"checkbox\" " .
										"value=\"" . $car["id"] . "\" " .
										"name=\"cbCarSelection[]\">" . 
									"</td>" .
								"</tr>";
						}
					?>
				</tbody>
			</table>
        </div>
      </div>
		
		<div class="form-group row">
        <div class="offset-sm-4 col-sm-8">
        <button type="submit" class="btn btn-primary" name="dispatchBn" id="submit">Dispatch</button>
			
        </div>
      </div>
      
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
