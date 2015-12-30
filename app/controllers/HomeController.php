<?php

class HomeController extends BaseController {

	public function add_user() {
		$requirements = ['device_id'];
		$check  = self::check_requirements($requirements);
		if($check) {
			return Error::make(0,100,$check);
		}
		$device_id = Input::get('device_id');
		$user = User::where('device_id', '=', $device_id)->first();
		if (!is_null($user)) {
			return Error::success("User exists!", array('user_id' => $user->user_id));
		}
		$user = new User;
		$user->device_id = $device_id;
		try {
			$user->save();
			return Error::success("User successfully added!", array('user_id' => $user->id));
		} catch(Exception $e) {
			return Error::make(101,101,$e->getMessage());
		}
	}

	public function add_experiment() {
		
	}
}
