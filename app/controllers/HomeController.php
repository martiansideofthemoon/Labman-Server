<?php

class HomeController extends BaseController {

	public function add_user() {
		$requirements = ['device_id'];
		$check  = self::check_requirements($requirements);
		if($check) {
			return Error::make(0, 100, $check);
		}
		$device_id = Input::get('device_id');
		$user = User::where('device_id', '=', $device_id)->first();
		if (!is_null($user)) {
			return Error::success("User exists!", array('user_id' => intval($user->user_id)));
		}
		$user = new User;
		$user->device_id = $device_id;
		try {
			$user->save();
			return Error::success("User successfully added!", array('user_id' => $user->id));
		} catch(Exception $e) {
			return Error::make(101, 101, $e->getMessage());
		}
	}

	public function add_experiment() {
		$requirements = ['title', 'specifications'];
		$check  = self::check_requirements($requirements);
		if($check) {
			return Error::make(0, 100, $check);
		}
		try {
			$specifications = json_decode(Input::get('specifications'), true);
		} catch (Exception $e) {
			return Error::make(101, 101, $e->getMessage());
		}
		if (is_null($specifications) || !array_key_exists("columns", $specifications)) {
			return Error::make(1, 9);
		}
		if (!is_array($specifications["columns"]) || self::is_assoc($specifications["columns"])) {
			return Error::make(1, 3);
		}

		// Checking whether experiment is valid.
		foreach ($specifications["columns"] as $column) {
			if (array_key_exists("title", $column) &&
			array_key_exists("unit", $column) &&
			array_key_exists("subcolumns", $column)) {
				if (!is_string($column["title"])) {
					return Error::make(1, 4);
				}
				if (!is_string($column["unit"])) {
					return Error::make(1, 5);
				}
				if (!is_null($column["subcolumns"])) {
					if (!is_array($column["subcolumns"]) || self::is_assoc($column["subcolumns"])) {
						return Error::make(1, 7);
					}
					foreach ($column["subcolumns"] as $subcolumn) {
						if (array_key_exists("title", $subcolumn) && array_key_exists("unit", $subcolumn)) {
							if (!is_string($subcolumn["title"])) {
								return Error::make(1, 4);
							}
							if (!is_string($subcolumn["unit"])) {
								return Error::make(1, 5);
							}
						} else {
							return Error::make(1, 8);
						}
					}
				}
			} else {
				return Error::make(1, 6);
			}
		}

		$experiment = new Experiment;
		$experiment->title = Input::get('title');
		$experiment->specifications = json_encode($specifications);
		try {
			$experiment->save();
			return Error::success("Experiment successfully added!", array('exp_id' => $experiment->id));
		} catch(Exception $e) {
			return Error::make(101, 101, $e->getMessage());
		}
	}

	public function search_experiments() {
		$requirements = ['title'];
		$check  = self::check_requirements($requirements);
		if($check) {
			return Error::make(0, 100, $check);
		}
		$wildcard = "%".Input::get('title')."%";
		$experiments = Experiment::select('title', 'exp_id')->where('title', 'like', $wildcard)->get();
		if (sizeof($experiments) > 0) {
			foreach ($experiments as $experiment) {
				$experiment->exp_id = intval($experiment->exp_id);
			}
			return Error::success("Found some results!", array('results' => $experiments));
		} else {
			return Error::success("Found no results!", array('results' => $experiments));
		}
	}

	public function get_experiment() {
		$requirements = ['exp_id'];
		$check  = self::check_requirements($requirements);
		if($check) {
			return Error::make(0, 100, $check);
		}
		$experiment = Experiment::where('exp_id', '=', Input::get('exp_id'))->first();
		if (is_null($experiment)) {
			return Error::make(1, 10);
		}
		$experiment->exp_id = intval($experiment->exp_id);
		$experiment->specifications = json_decode($experiment->specifications, true);
		return Error::success("Experiment found!", array('experiment' => $experiment));
	}

	public function add_result() {
		$requirements = ['exp_id', 'user_id', 'data'];
		$check  = self::check_requirements($requirements);
		if($check) {
			return Error::make(0, 100, $check);
		}
		$experiment = Experiment::where('exp_id', '=', Input::get('exp_id'))->first();
		if (is_null($experiment)) {
			return Error::make(1, 10);
		}
		$user = User::where('user_id', '=', Input::get('user_id'))->first();
		if (is_null($user)) {
			return Error::make(1, 1);
		}
		if (Input::has('result_id')) {
			$res = Result::where('result_id', '=', Input::get('result_id'))->first();
			if (intval(Input::get('exp_id')) != intval($res->exp_id)) {
				return Error::make(1, 11);
			}
			if (intval(Input::get('user_id')) != intval($res->user_id)) {
				return Error::make(1, 12);
			}
		}
		$columns = json_decode($experiment->specifications, true)["columns"];
		$results = json_decode(Input::get('data'), true);
		foreach ($results as $reading) {
			foreach($columns as $column) {
				if (array_key_exists($column["title"], $reading)) {
					if (!is_null($column["subcolumns"]) && sizeof($column["subcolumns"]) > 0) {
						foreach ($column["subcolumns"] as $subcolumn) {
							if (!array_key_exists($subcolumn["title"], $reading[$column["title"]])) {
								return Error::make(101, 101, "Column ".$column["title"]." has missing subcolumn ".$subcolumn["title"]);
							}
						}
					}
				} else {
					return Error::make(101, 101, "Missing column ".$column["title"]);
				}
			}
		}
		if (Input::has('result_id')) {
			try {
				Result::where('result_id', '=', intval(Input::get('result_id')))->update(array(
					'data' => json_encode($results),
				));
				return Error::success("Results successfully updated", array('result_id' => intval(Input::get('result_id'))));
			}
			catch (Exception $e) {
				return Error::make(101,101,$e->getMessage());
			}
		} else {
			$res = new Result;
			$res->data = json_encode($results);
			$res->user_id = intval(Input::get('user_id'));
			$res->exp_id = intval(Input::get('exp_id'));
			try {
				$res->save();
				return Error::success("Result successfully added!", array('result_id' => $res->id));
			} catch(Exception $e) {
				return Error::make(101, 101, $e->getMessage());
			}
		}
	}
}
