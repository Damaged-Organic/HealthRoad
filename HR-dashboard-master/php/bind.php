<?php
	sleep(1);
	$decoded = json_decode($_POST["data"], true);

	if(isset($decoded["isBinded"]) && isset($decoded["parentID"])){
		$response["message"] = $decoded["isBinded"] ? "Объект отвязан успешно" : "Объект привязан успешно";
		echo json_encode($response);
	} else{
		header("HTTP/1.1 500 Internal Server error");
		echo "Сбой при работе система, пожалуйтса, попробуйте повторить попытку чуть позже";
	}	

	
?>