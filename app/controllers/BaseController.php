<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	public function check_requirements($requirements) {
		foreach ($requirements as $value) {
			if(!Input::has($value))
				return $value;
		}
		return false;
	}

	public function is_assoc(array $array)
	{
	    // Keys of the array
	    $keys = array_keys($array);

	    // If the array keys of the keys match the keys, then the array must
	    // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
	    return array_keys($keys) !== $keys;
	}

}
