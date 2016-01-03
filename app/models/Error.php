<?php
class Error{
	private static $error_messages =  array(
		#error code to error message mapping
		'0' => "Some Error Occured",
		'401' => "Authentication Failed",
		'2' => "Authentication key Required",
		'404' => "404 Error : URL Not Found",
		'100' => "Input field required : " ,
		'101' => "" ,
		'1' => "Invalid user ID!",
		'3' => "specifications->columns need to be array of columns",
		'4' => "column / subcolumn should have string title",
		'5' => "column / subcolumn should have string unit",
		'6' => "column should have title, unit and subcolumn field",
		'7' => "subcolumns should be null or have an array",
		'8' => "subcolumn should have a unit and title field",
		'9' => "specifications must have columns field",
		'10' => "No experiment found!",
		'11' => "This result does not have this experiment id",
		'12' => "This result does not have this user id"
	);
	// Error type
	public static function make($type=0 , $code = 0 , $field="")
	{
		$message=self::$error_messages[$code];
		if($code == 100 || $code == 101)
			$message.=$field;
		$contents= array('error' => 1, 'message' => $message);
		if($type >= 110)
			$status = $type;
		else
			$status = 412;
		$status=200;
		$response = Response::make($contents, $status,array('statusText'=>$message));
		return $response;
	}
	public static function success($message="Success",$data= array())
	{
		$contents= array('error' => 0, 'message' => $message);
		$contents=array_merge($contents,$data);
		$status = 200;
		$response = Response::make($contents, $status,array('statusText'=>$message));
		return $response;
	}
}