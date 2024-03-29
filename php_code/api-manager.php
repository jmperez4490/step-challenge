<?php
	class apiManager {
		private $conn = null;
		function __construct() {
			$this -> conn = new mysqli("localhost","root","testingplatform","step-challenge");
		}

		function Control($data) {
			$data = $this -> cleanString($data);
			switch ($data['action']) {
				case 'login':
					return $this -> login_account($data);
				break;
				case 'register':
					echo $this -> registerUser($data);
				break;
				case 'addSteps':
					echo $this -> recordSteps($data);
				break;
				case 'getSteps':
					echo $this -> readSteps($data);
				break;
				case 'update-profile':
					echo $this -> updateProfile($data);
				break;
				default:
					echo $data['action'];
					break;
			}
		}

		private function updateProfile($data) {
			$update = "UPDATE `step-challenge`._userDB SET first_name = '{$data['_name']}',username = '{$data['_email']}', password = PASSWORD('{$data['_pass']}') WHERE _id = '{$_SESSION['_id']}' AND token_number = '{$_SESSION['token']}'";
			$this -> conn -> autocommit(FALSE);
			$this -> conn -> query($update);
			if($this -> conn -> affected_rows == 1){
					$this -> conn -> commit();
					return json_encode(array('result'=>TRUE));
				}
				else {
					$this -> conn -> rollback();
					return json_encode(array('result'=>FALSE,'respond'=>$this -> conn -> error));
				}
		}

		private function login_account($data) {
			$user = "SELECT first_name, last_name, _id, team FROM `step-challenge`._userDB WHERE username = '{$data['user-name']}' and password = PASSWORD('{$data['user-password']}') LIMIT 1";
			$result = $this -> conn -> query($user);
			if($result -> num_rows > 0) {
				$responds = null;
				while($row = $result -> fetch_assoc()) {
					$responds = $row;
				}
				$token = $this -> generateToken();
				$user = "UPDATE `step-challenge`._userDB SET token_number = '{$token}' WHERE _id = '{$responds['_id']}'";
				if($this -> conn -> query($user) == TRUE) {
					$responds['token'] = $token;
					return $responds;
				}
				else
				{
					return array("Error:". $this -> conn -> error);
				}
			}
			else
			{
				return array("Error:". $this -> conn -> error);
			}
		}

		private function generateToken($length = 50) {
			return $this -> conn -> real_escape_string(bin2hex(random_bytes($length)));
		}

		private function cleanString($data) {
			$answers = null;
			foreach ($data as $key => $value) {
				$key = $this -> conn -> real_escape_string($key);
				$value = $this -> conn -> real_escape_string($value);
				$answers[$key] = $value;
			}
			return $answers;
		}

		private function registerUser($data) {
			$check = "SELECT count(*) as exist FROM `step-challenge`._userDB WHERE username = '{$data['user-name']}'";
			$result = $this -> conn -> query($check);
			if($result -> num_rows > 0) {
				$responds = 0;
				while($row  = $result -> fetch_assoc()) {
					$responds += $row['exist'];
				}
				if($responds > 0)
				{
					echo "This user already exist";
					return;
				}
			}
			$add = "INSERT INTO `step-challenge`._userDB(first_name, last_name, username, password) VALUES('{$data['first-name']}','{$data['last-name']}','{$data['user-name']}',PASSWORD('{$data['user-password']}'))";
			if($this -> conn -> query($add) == TRUE) {
				echo "You have successfuly being added. Please click <a class = 'login-link' href = './'>here</a> to login.";
				return;
			}
			else
			{
				echo "Error:". $this -> conn -> error;
				return;
			}
		}

		private function recordSteps($data) {
			$checkUser = "SELECT COUNT(*) FROM `step-challenge`._userDB WHERE _id = '{$_SESSION['_id']}' AND token_number = '{$_SESSION['token']}' LIMIT 1";
			$result = $this -> conn -> query($checkUser);
			if($result -> num_rows > 0) {
				$this -> conn -> autocommit(FALSE);
				$addStep = "INSERT INTO `step-challenge`.step_tracker(week_date,steps,walker_id) VALUES('{$data['timestamp']}','{$data['steps']}','{$_SESSION['_id']}') ON DUPLICATE KEY UPDATE steps = '{$data['steps']}'";
				$this -> conn -> query($addStep);
				if($this -> conn -> affected_rows == 1 || $this -> conn -> affected_rows == 2){
					$this -> conn -> commit();
					$info['week'] = $data['timestamp'];
					$info = $this -> getWeekInfo($info);
					
					$checkStatus = "SELECT SUM(t.steps) as steps FROM `step-challenge`.step_tracker t WHERE t.walker_id = '{$_SESSION['_id']}' AND t.week_date BETWEEN CAST('{$info['start']}' AS DATE) AND CAST('{$info['end']}' AS DATE)  GROUP BY t.walker_id LIMIT 1";
					$result = $this -> conn -> query($checkStatus);
					$responds = 0;
					if($result -> num_rows > 0) {
						while($row  = $result -> fetch_assoc()) {
							$responds += $row['steps'];
						}
					}
					$goal = "SELECT g.steps AS steps FROM `step-challenge`.step_goal g WHERE g.week_number = '{$info['week']}'";
					$_goal = $this -> conn -> query($goal);
					$goal = 0;
					if($_goal -> num_rows > 0) {
						while($row  = $_goal -> fetch_assoc()) {
							$goal += $row['steps'];
						}
					}
					$info['status'] = $responds - $goal;
					$info['result'] = TRUE;
					return json_encode($info);
				}
				else {
					$this -> conn -> rollback();
					return json_encode(array('result'=>FALSE,'respond'=>$this -> conn -> error));
				}
			}
			return implode($data);
		}

		private function readSteps($data) {
			$steps = "SELECT DAY(week_date) AS day, MAX(steps) AS steps FROM `step_tracker` WHERE walker_id = '{$_SESSION['_id']}' AND MONTH(week_date) = '{$data['month']}' GROUP BY DAY(week_date)";
			$this -> conn -> begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
			$steps = $this -> conn -> query($steps);
			if($steps -> num_rows > 0) {
				$responds = null;
				$t = 0;
				while($row = $steps -> fetch_assoc()) {
					$responds[$row['day']] = $row['steps'];
					$t++;
				}
				return json_encode($responds);
			}
			else
				return json_encode(array());
		}

		function getWeekInfo($data) {
			$dateTime = new DateTime();
			$date = new DateTime($data['week']);
			$ret['week'] =  $date -> format('W');
			$data['year'] = $date -> format('Y');
			$ret['start'] = $dateTime -> setISODate($data['year'],$ret['week']) -> format('Y-m-d');
			$ret['end'] = $dateTime -> modify('+6 days') -> format('Y-m-d');

			return $ret;
		}

		function getProfileInfo() {
			$userInfo = "SELECT u.first_name, u.username, team_name, null AS team_captain FROM `step-challenge`._userDB u INNER JOIN  `step-challenge`.team_list t ON u.team = team_id WHERE u._id = '{$_SESSION['_id']}'";
			$result = $this -> conn -> query($userInfo);
			if($result -> num_rows > 0) {
				$responds = null;
				while($row = $result -> fetch_assoc()) {
					$responds = $row;
				}
				return $responds;
			}
		}

		function teamStatus() {
			$teamStatus = "SELECT u.first_name, SUM(t.steps) AS steps, g.steps as goal, WEEK(week_date,1) AS week FROM `step-challenge`.`step_tracker` t INNER JOIN _userDB u ON walker_id = u._id INNER JOIN `step-challenge`.step_goal g ON WEEK(week_date,1) = g.week_number WHERE u.team = '{$_SESSION['team']}' GROUP BY walker_id, WEEK(week_date,1),g.steps ORDER BY steps ASC, week ASC";
			$result = $this -> conn -> query($teamStatus);
			$responds = array();
			if($result -> num_rows > 0) {
				while($row = $result -> fetch_assoc()) {
					$week = $row['week'];
					unset($row['week']);
					if(array_key_exists($week, $responds))
					{
						array_push($responds[$week], $row);
					}
					else
					{
						$responds[$week] = array($row);
					}
					
				}
				return $responds;
			}
		}

		function competitorStatus() {
			$competitor = "SELECT team_name, ((SUM(t.steps) / g.steps)*100)  AS steps, WEEK(week_date,1) AS week FROM `step-challenge`.`step_tracker` t INNER JOIN `step-challenge`._userDB u ON walker_id = u._id INNER JOIN `step-challenge`.team_list on team = team_id INNER JOIN `step-challenge`.step_goal g ON WEEK(week_date,1) = g.week_number GROUP BY team, WEEK(week_date,1),team_name, g.steps ORDER BY steps ASC";
			$result = $this -> conn -> query($competitor);
			$responds = array();
			if($result -> num_rows > 0) {
				while($row = $result -> fetch_assoc()) {
					$week = $row['week'];
					unset($row['week']);
					if(array_key_exists($week, $responds))
					{
						$team_name = $row['team_name'];
						$responds[$week][$team_name] = $row['steps'];
					}
					else
					{
						$team_name = $row['team_name'];
						$responds[$week][$team_name] = $row['steps'];
					}
				}
				return $responds;
			}
			else {
				echo $this -> conn -> error;
			}
		}
	}
?>
