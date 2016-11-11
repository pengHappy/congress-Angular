<!-- php -->
<?php

define("urlPrefix", "http://104.198.0.197:8080/");
define("apiKey", "c6ac271017ed4e1aa3aa245e4bf7032e");
define("facebookPrefix", "https://www.facebook.com/");
define("twitterPrefix", "https://www.twitter.com/");
define("imgPrefix", "https://theunitedstates.io/images/congress/225x275/");

function getQueryUrl() {
		
	$stateArr = array(
			"alabama" => "AL",
			"alaska" => "AK",
			"montana" => "MT",
			"nebraska" => "NE",
			"arizona" => "AZ",
			"nevada" => "NV",
			"arkansas" => "AR",
			"new hampshire" => "NH",
			"california" => "CA",
			"new jersey" => "NJ",
			"colorado" => "CO",
			"new mexico" => "NM",
			"connecticut" => "CT",
			"new york" => "NY",
			"￼delaware" => "DE",
			"north carolina" => "NC",
			"district ofcolumbia" => "DC",
			"north dakota" => "ND",
			"florida" => "FL",
			"ohio" => "OH",
			"georgia" => "GA",
			"oklahoma" => "OK",
			"hawaii" => "HI",
			"oregon" => "OR",
			"idaho" => "ID",
			"pennsylvania" => "PA",
			"illinois" => "IL",
			"rhode island" => "RI",
			"indiana" => "IN",
			"south carolina" => "SC",
			"iowa" => "IA",
			"south dakota" => "SD",
			"kansas" => "KS",
			"tennessee" => "TN",
			"kentucky" => "KY",
			"texas" => "TX",
			"louisiana" => "LA",
			"utah" => "UT",
			"maine" => "ME",
			"vermont" => "VT",
			"maryland" => "MD",
			"virginia" => "VA",
			"massachusetts" => "MA",
			"washington" => "WA",
			"michigan" => "MI",
			"west virginia" => "WV",
			"minnesota" => "MN",
			"wisconsin" => "WI",
			"mississippi" => "MS",
			"wyoming" => "WY",
			"missouri" => "MO",
	);
		
	$dbType = $_GET["category"]; // category from navigation bar
								// dbType is just the same as category, remember
	$chamber = $_GET["chamber"];
	$state = $_GET["state"];
    $bioguideId = $_GET["bioguideId"];
	$apikey = apiKey;
	$queryUrl = urlPrefix; // https://congress.api.sunlightfoundation.com/
		
	// based on database type, if else, not a good design
	if($dbType == "legislators") {
		
		if($bioguideId != null) { // legislator view detail
            $queryUrl .= $dbType . "?bioguide_id=" . $bioguideId . "&apikey=" . $apikey;
        }
        else { // all results
            $queryUrl = $queryUrl . $dbType . "?" . "apikey=" . $apikey;
        }
	}
    else if($dbType == "committees") {
        // legislator view detail
        if($bioguideId != null) {
            $queryUrl .= "committees?member_ids=" . $bioguideId . "&apikey=" . $apikey;
        }
        // committee table
        else {
            $queryUrl .= "committees?&apikey=" . $apikey;
        }
    }
    else if($dbType == "bills") {
        // legislator view detail
        if($bioguideId != null) {
            $queryUrl .= "bills?sponsor_id=" . $bioguideId . "&apikey=" . $apikey;
        }
        // bill view detail
        else if($_GET["billId"] != null) {
            $billId = $_GET["billId"];
            $queryUrl = $queryUrl . $dbType . "?bill_id=" . $billId . "&apikey=" . $apikey;
        }
        else { // all results
            $active = $_GET["active"];
            if($active == "true") {
                $queryUrl = $queryUrl . $dbType . "?history.active=true&apikey=" . $apikey;
            }
            else if($active == "false") {
                $queryUrl = $queryUrl . $dbType . "?history.active=false&apikey=" . $apikey;
            }
        }
    }
    
	$queryUrl .= "&per_page=all";
    
    return $queryUrl;
}
		
// 		$keyword = $_GET["keyword"];
// 		$keyword = trim($keyword);
// 		$secondPara = "query";

// 		if(array_key_exists(strtolower($keyword), $stateArr)) { // state name
// 			$keyword = $stateArr[strtolower($keyword)];
// 			$secondPara = "state";
// 			$queryUrl = $queryUrl . $dbType . "?chamber=" . $chamber . "&" . $secondPara . "=" .
// 					$keyword . "&apikey=" . $apikey;
// 		}
// 		else {
// 			// filter space
// 			$keyword = rawurlencode($keyword);
				
// 			// partial & full name query
// 			if(strpos($keyword, '%') !== false) {
// 				$firstSpacePos = strpos($keyword, '%');
// 				$firstName = substr($keyword, 0, $firstSpacePos);
// 				$lastName = substr($keyword, $firstSpacePos + 3);

// 				// url prefix
// 				$queryUrl = $queryUrl . $dbType . "?chamber=" . $chamber;

// 				// full name
// 				if('A' <= $firstName[0] && $firstName[0] <= 'Z' && 'A' <= $lastName[0] && $lastName[0] <= 'Z') {
// 					$queryUrl .= "&first_name=" . $firstName . "&last_name=" . $lastName;
// 				}

// 				// partial name
// 				// if partial name contains a full name, oh my god let it go
// 				else if('A' <= $firstName[0] && $firstName[0] <= 'Z') {
// 					$queryUrl .= "&first_name=" . $firstName . "&query=" . $lastName;
// 				}
// 				else if('A' <= $lastName[0] && $lastName[0] <= 'Z'){
// 					$queryUrl .= "&last_name=" . $lastName . "&query=" . $firstName;
// 				}

// 				// no use of first_name or last_name field, just query
// 				else {
// 					$queryUrl .= "&query=" . $keyword;
// 				}

// 				$queryUrl .= "&apikey=" . $apikey;

// 			}
// 			else {
// 				$queryUrl = $queryUrl . $dbType . "?chamber=" . $chamber . "&" . $secondPara . "=" .
// 						$keyword . "&apikey=" . $apikey;
// 			}
// 		}
// 		// 				return $queryUrl;
// 	}
// 	else if($dbType == "bills") {
// 		$keyword = $_GET["keyword"];
// 		$queryUrl .= $dbType . "?bill_id=" . $keyword . "&chamber=" . $_GET["chamber"] . "&apikey=" . $apikey;
// 	}
// 	else if($dbType == "committees") {
// 		$keyword = $_GET["keyword"];
// 		$queryUrl .= $dbType . "?committee_id=" . $keyword . "&chamber=" . $_GET["chamber"] . "&apikey=" . $apikey;
// 	}
// 	else if($dbType == "amendments") {
// 		$keyword = $_GET["keyword"];
// 		$queryUrl .= $dbType . "?amendment_id=" . $keyword . "&chamber=" . $_GET["chamber"] . "&apikey=" . $apikey;
// 	}
		


// check if View Detail is triggered
// $rawdata = file_get_contents('php://input');

// if($rawdata != null) {
		
// 	$ans = "";
// 	$json_decode = json_decode($rawdata, true);
		
// 	// bill detail
// 	if(array_key_exists("bill_id", $json_decode)) {
// 		$ans .= '<div class="detail_bill"><table class="detail_bill">';
// 		$ans .= "<tr><th>Bill ID</th>" . "<td>" . $json_decode["bill_id"] . "</td></tr>";

// 		// short_title null
// 		if($json_decode["short_title"] == null) {
// 			$ans .= "<tr><th>Bill Title</th><td>NA</td></tr>";
// 		}
// 		else {
// 			$ans .= "<tr><th>Bill Title</th>" . "<td>" . $json_decode["short_title"] . "</td></tr>";
// 		}

// 		$ans .= "<tr><th>Sponser</th>" . '<td>' . $json_decode["sponsor"]["title"] . $json_decode["sponsor"]["first_name"] . $json_decode["sponsor"]["last_name"] . '</td></tr>';
// 		$ans .= "<tr><th>Introduced On</th>" . "<td>" . $json_decode["introduced_on"] . "</td></tr>";
// 		$ans .= "<tr><th>Last action with date</th>" . "<td>" . $json_decode["last_version"]["version_name"] . "," . $json_decode["last_action_at"] . "</td></tr>";

// 		// short_title -> pdf null
// 		if($json_decode["short_title"] == null) {
// 			$ans .= '<tr><th>Bill URL</th>' . '<td><a href="' . $json_decode["last_version"]["urls"]["pdf"] . '">' . 'NA' . '</a>' . '</td></tr>';
// 		}
// 		else {
// 			$ans .= '<tr><th>Bill URL</th>' . '<td><a href="' . $json_decode["last_version"]["urls"]["pdf"] . '">' . $json_decode["short_title"] . '</a>' . '</td></tr>';
// 		}

// 		$ans .= "</table></div>";
// 	}
// 	// legislator detail
// 	else if(array_key_exists("bioguide_id", $json_decode)){
// 		$ans .= '<div class="detail"><img class="center" src="' . imgPrefix . $json_decode["bioguide_id"] . '.jpg"><br><br><br>';
// 		$ans .= '<table class="detail">';
// 		$ans .= "<tr><th>Full Name</th>" . "<td>" . $json_decode["title"] . " " . $json_decode["first_name"] . " " . $json_decode["last_name"] . "</td></tr>";
// 		$ans .= "<tr><th>Term Ends on</th>" . "<td>" . $json_decode["term_end"] . "</td></tr>";

// 		// website (null)
// 		if($json_decode["website"] == null) {
// 			$ans .= '<tr><th>Website</th><td>NA</td></tr>';
// 		}
// 		else {
// 			$ans .= "<tr><th>Website</th>" . '<td><a href="' . $json_decode["website"] . '">' . $json_decode["website"] . '</a>' .  '</td></tr>';
// 		}

// 		$ans .= "<tr><th>Office</th>" . "<td>" . $json_decode["office"] . "</td></tr>";

// 		// Facebook (null)
// 		if($json_decode["facebook_id"] == null) {
// 			$ans .= '<tr><th>Facebook</th><td>NA</td></tr>';
// 		}
// 		else {
// 			$ans .= "<tr><th>Facebook</th>" . '<td><a href="' . facebookPrefix . $json_decode["facebook_id"] . '">' . $json_decode["first_name"] . " " . $json_decode["last_name"] . '</a>' . "</td></tr>";
// 		}

// 		// Twitter (null)
// 		if($json_decode["twitter_id"] == null) {
// 			$ans .= '<tr><th>Twitter</th><td>NA</td></tr>';
// 		}
// 		else {
// 			$ans .= "<tr><th>Twitter</th>" . '<td><a href="' . twitterPrefix . $json_decode["twitter_id"] . '">' . $json_decode["first_name"] . " " . $json_decode["last_name"] . '</a>' . "</td></tr>";
// 		}

// 		$ans .= "</table></div>";
// 	}
// 	// 			$ans .= "detail";
// 	echo $ans;
// }

if (isset($_GET["category"])) {
		
	$category = $_GET["category"];
		
	$queryUrl = getQueryUrl();
	$json = file_get_contents($queryUrl);
	
	echo $json;
}
		
// 	// zero results for the request
// 	if($firstRes["count"] == 0) {
// 		echo '<div class="noResult">The API returned zero requests for the request</div>';
// 		return;
// 	}
		
// 	// display search result based on select database
// 	// if else, not a good design
// 	if($_GET["category"] == "legislators") {
// 		$ans .= '<table class="firstRes"><tr><th>Name</th><th>State</th><th>Chamber</th><th>View Details</th></tr>';
// 		$len = $firstRes["count"];
// 		for($i = 0; $i < $len; $i++) {
// 			$tuple = $firstRes["results"];
// 			$ans .= "<tr>" . "<td>" . $tuple[$i]["first_name"] . " " . $tuple[$i]["last_name"] . "</td>";
// 			$ans .= "<td>" . $tuple[$i]["state_name"] . "</td>";
// 			$ans .= "<td>" . $tuple[$i]["chamber"] . "</td>";
// 			$ans .= '<td><a href=\'\' onclick=\'return getViewDetailsOutPut(' . json_encode($tuple[$i]) . '); \'>View Details</a></td>';
// 			$ans .= "</tr>";
// 		}
// 		$ans .= "</table>";
// 	}
// 	else if($_GET["selectDB"] == "bills") {
// 		$ans .= '<table class="firstRes"><tr><th>Bill ID</th><th>Short Title</th><th>Chamber</th><th>Details</th></tr>';
// 		$len = $firstRes["count"];
// 		for($i = 0; $i < $len; $i++) {
// 			$tuple = $firstRes["results"];
// 			$ans .= "<tr>" . "<td>" . $tuple[$i]["bill_id"] . "</td>";
				
// 			// short_title
// 			if($tuple[$i]["short_title"] == null) {
// 				$ans .= "<td>" . "NA" . "</td>";
// 			}
// 			else {
// 				$ans .= "<td>" . $tuple[$i]["short_title"] . "</td>";
// 			}
				
// 			$ans .= "<td>" . $tuple[$i]["chamber"] . "</td>";
// 			$ans .= '<td><a href=\'\' onclick=\'return getViewDetailsOutPut(' . json_encode($tuple[$i]) . '); \'>View Details</a></td>';
// 			$ans .= "</tr>";
// 		}
// 		$ans .= "</table>";
// 	}
// 	else if($_GET["selectDB"] == "committees") {
// 		$ans .= '<table class="firstRes"><tr><th>Committee ID</th><th>Committee Name</th><th>Chamber</th></tr>';
// 		$len = $firstRes["count"];
// 		for($i = 0; $i < $len; $i++) {
// 			$tuple = $firstRes["results"];
// 			$ans .= "<tr>" . "<td>" . $tuple[$i]["committee_id"] . "</td>";
// 			$ans .= "<td>" . $tuple[$i]["name"] . "</td>";
// 			$ans .= "<td>" . $tuple[$i]["chamber"] . "</td>";
// 			$ans .= "</tr>";
// 		}
// 		$ans .= "</table>";
// 	}
// 	else if($_GET["selectDB"] == "amendments") {
// 		$ans .= '<table class="firstRes"><tr><th>Amendment ID</th><th>Amendment Type</th><th>Chamber</th><th>Introduced on</th></tr>';
// 		$len = $firstRes["count"];
// 		for($i = 0; $i < $len; $i++) {
// 			$tuple = $firstRes["results"];
// 			$ans .= "<tr>" . "<td>" . $tuple[$i]["amendment_id"] . "</td>";
// 			$ans .= "<td>" . $tuple[$i]["amendment_type"] . "</td>";
// 			$ans .= "<td>" . $tuple[$i]["chamber"] . "</td>";
// 			$ans .= "<td>" . $tuple[$i]["introduced_on"] . "</td>";
// 			$ans .= "</tr>";
// 		}
// 		$ans .= "</table>";
// 	}
		
// 	echo $ans;



?>