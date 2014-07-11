<!doctype HTML>
<html>
<head>
</head>
<body>

<?php 

/* This is the code to sort and display team names with links ordered by states and then by countries */

$event_id = 'XXXX';
$auth_token = 'XXXX';

$jsonTemp = file_get_contents('https://www.eventbriteapi.com/v3/events/'.$event_id.'/attendees/?token='.$auth_token);
$data1 = json_decode($jsonTemp, TRUE);

$count = $data1['pagination']['page_count'];
$object_count = $data1['pagination']['object_count'];
$count2 = $count * $object_count;

for($i=1;$i<=$count;$i++){
	$temp = json_decode(file_get_contents('https://www.eventbriteapi.com/v3/events/'.$event_id.'/attendees/?token='.$auth_token."&page=".$i), TRUE);
	$bigData[] = $temp;
}
//var_dump($bigData);

$States = array();
$Teams = array();
$Countries = array();
$teamToAdd = array();
$teamToAdd2 = array();

//Make an array of Countries that are NOT the united states
foreach ($bigData as $data) {
	for ($i=0; $i<50; $i++){
		$countryTemp = $data['attendees'][$i]['profile']['addresses']['ship']['country'];
		if( !in_array( $countryTemp, $Countries ) && $countryTemp != "US" ){
			$team_day = $data['attendees'][$i]['answers'][7]["answer"];
			if ( !is_null($team_day) ){
				array_push($Countries, $countryTemp);
			}
			
		}
	}
}
//Sort countries alphabetically
sort($Countries);

foreach ($bigData as $data) {
	//Make an array of states
	for ($i=0; $i<50; $i++){
		$data['attendees'][$i]['profile']['addresses']['ship']['region']."<br>";
		if($data['attendees'][$i]['profile']['addresses']['ship']['country']=="US"){
			$tempID = $data['attendees'][$i]['team']['id'];
			$team_day = $data['attendees'][$i]['answers'][7]["answer"];
			if ( !is_null($team_day) ){
				if( $tempID != "769399" && $tempID != null ){	

					//echo $data['attendees'][$i]['team']['id']."<br>";
					$stateTemp = $data['attendees'][$i]['profile']['addresses']['ship']['region'];
					//echo $stateTemp." ".$tempID."<br>";
					if( !in_array( $stateTemp, $States ) ){
						array_push($States, $stateTemp);
					}
				}
			}
		}
	}
}
	//Sort states alphabetically
sort($States);

foreach ($bigData as $data) {

	for ($i=0;$i<count($States);$i++){
		for ($j=0; $j<50; $j++){
			$team_id = $data['attendees'][$j]['team']['id'];
			$team_name = ucfirst($data['attendees'][$j]['team']['name']);
			$team_day = $data['attendees'][$j]['answers'][7]["answer"];
			$team_time = $data['attendees'][$j]['answers'][8]["answer"];
			$team_state = $data['attendees'][$j]['profile']['addresses']['ship']['region'];

			if ( $team_state == $States[$i] ){
				if ( !is_null($team_name)){
					if( !is_null($team_day) ){
						$teamInfo = "<a href='http://www.eventbrite.com/teams/".$team_id."'>".$team_name."</a>".
						" August ".$team_day." , Time: ".$team_time."<br>";
						$teamToAdd[] = array( 'state' => $team_state, 'info' => $teamInfo, 'name' => $team_name);
					}
				}
			}
		}
	}
}
//var_dump($teamToAdd);

foreach ($teamToAdd as $key => $row) {
    $teamName[$key]  = $row['name'];
    $state[$key] = $row['state'];
    $info[$key] = $row['info'];
}
array_multisort($state, SORT_ASC, $teamName, SORT_ASC, $info, SORT_DESC, $teamToAdd);

$usTeams = $teamToAdd;

foreach ($bigData as $data) {
	for ($i=0;$i<count($Countries);$i++){
		for ($j=0; $j<50; $j++){
			$team_id = $data['attendees'][$j]['team']['id'];
			$team_name = ucfirst($data['attendees'][$j]['team']['name']);
			$team_day = $data['attendees'][$j]['answers'][7]["answer"];
			$team_time = $data['attendees'][$j]['answers'][8]["answer"];
			$team_country = $data['attendees'][$j]['profile']['addresses']['ship']['country'];

			if ( $team_country == $Countries[$i] ){
				
				if( !is_null($team_day) ){

					$teamInfo = "<a href='http://www.eventbrite.com/teams/".$team_id."'>".$team_name."</a>".
					" August ".$team_day." , Time: ".$team_time."<br>";

					$teamArray = array( 'country' => $team_country, 'info' => $teamInfo, 'name' => $team_name);
					if( !in_array($teamArray, $teamToAdd2) ){
						$teamToAdd2[] = $teamArray;
					}
				}
			}
		}
	}
}
if(isset($countries)){
	foreach ($teamToAdd2 as $key => $row) {
		$teamName2[$key]  = $row['name'];
		$country[$key] = $row['state'];
		$info2[$key] = $row['info'];
	}

	if(is_array($teamToAdd2)){
		array_multisort($country, SORT_ASC, $teamName2, SORT_ASC, $info2, SORT_DESC, $teamToAdd2);
	}
}
$foreignTeams = $teamToAdd2;

echo "<h2>United States</h2>";
for ($i=0;$i<count($States);$i++){
	echo "<h3>".$States[$i]."</h3>";
	for ($j=0; $j<count($usTeams); $j++){
		if( $States[$i]==$usTeams[$j]['state']){	
			echo $usTeams[$j]['info'];
		}
	}
}

if(isset($Countries)){
echo "<h2>Other Countries</h2>";
	for ($i=0;$i<count($Countries);$i++){
		echo "<h3>".$Countries[$i]."</h3>";
		for ($j=0; $j<count($foreignTeams); $j++){
			if( $Countries[$i]==$foreignTeams[$j]['country']){
				echo $foreignTeams[$j]['info'];
			}
		}
	}
}

?>

</body>
</html>