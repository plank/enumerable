<?php

/**
 * Enum Model behavior
 *
 * Allows you to fake MySQL ENUM types
 *
 * @package wowwee
 * @subpackage wowwee.models.behaviors
 */
class EnumerableBehavior extends ModelBehavior {

	/**
	 * Behavior settings, indexed by model attachment
	 *
	 * @var array
	 * @access public
	 */
	public $settings = array();

	/**
	 * Setup behavior configurations
	 *
	 * @param object $Model Model object reference
	 * @param array $settings Behavior configuration settings
	 * @return void
	 */
	public function setup(&$Model, $settings = array()) {
		if (empty($settings)) {
			trigger_error('You must define at least one Enumerable column', E_USER_WARNING);
		}

		$this->settings[$Model->name] = $settings;
	}

	/**
	 * Behavior afterFind callback
	 *
	 * @param object $Model Model object reference
	 * @param array $results Results of find operation
	 * @return array Modified result set
	 */
	public function afterFind(&$Model, $results) {
		$name = $Model->name;
		$settings =  $this->settings[$name];

		foreach ($results as &$result) {
			if (isset($result[$name])) {
				foreach ($this->settings[$Model->name] as $field => $enums) {
					if (isset($result[$name][$field])) {
						if (isset($settings[$field][$result[$name][$field]])) {
							$result[$name][$field] = $settings[$field][$result[$name][$field]];
						}
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Returns the enumerable options for specified field
	 *
	 * @param object $Model Model object reference
	 * @param string $field Enumerable field to query
	 * @return array Enumerable fields, or an empty array if invalid field name,
	 * @access public
	 */
	public function enumerate(&$Model, $field) {
		if (!isset($this->settings[$Model->name][$field])) {
			return array();
		}

		return $this->settings[$Model->name][$field];
	}

}
?>