<?php
	class apiManager {
		private $conn = null;
		function __construct() {
			$this -> conn = new mysqli("localhost","root","stepchallenge","step-challenge");
		}

		function functionControl($data) {
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
			$user = "SELECT first_name, last_name, _id FROM `step-challenge`._userDB WHERE username = '{$data['user-name']}' and password = PASSWORD('{$data['user-password']}') LIMIT 1";
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
					return json_encode(array('result'=>TRUE));
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
	}
?>
