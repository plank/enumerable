<?php
/**
 * Enumerable: Because ENUMS can suck, and humans like words.
 *
 * @copyright     Copyright 2010, Plank Design (http://plankdesign.com)
 * @license       http://opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Enumerable Behavior
 *
 * ENUM column types are not supported by CakePHP for a variety of reasons.
 * However, there are occasions where you want exactly what an ENUM column type would provide.
 *
 * The Enumerable behavior attempts to bridge this gap by providing some transparent
 * rewriting of fields marked as enumerable when returned in `Model::find()` calls.
 *
 * A very typical use-case for Enumerable would be for a `gender` field in a user profile
 * model. Without Enumerable, you have three choices:
 *
 * 1) use ENUMs, which are not supported in model schemas, but some workarounds
 * exists (e.g. custom Datasources).
 *
 * 2) Define the column type as an `integer` or `char(1)`, and use class constants in the
 * model, e.g. Profile::MALE would map to `0` or `m`. This could be viable for some, but
 * requires some mental bookeeping.
 *
 * See the README for example configurations and how the method behaviors can be used
 * to make your ENUMerable fields simpler to manage.
 *
 */
class EnumerableBehavior extends ModelBehavior {

	/**
	 * Behavior settings, indexed by model.
	 *
	 * @var array Settings for this behavior, indexed by Model name or alias.
	 */
	public $settings = array();

	/**
	 * Setup behavior configurations.
	 *
	 * @param object $Model Model object reference.
	 * @param array $settings Behavior configuration settings.
	 * @return void
	 */
	public function setup(&$Model, $settings = array()) {
		if (empty($settings)) {
			trigger_error('You must define at least one Enumerable column', E_USER_WARNING);
		}
		$this->settings[$Model->name] = $settings;
	}

	/**
	 * Behavior afterFind callback.
	 *
	 * @param object $Model Model object reference.
	 * @param array $results Results of find operation.
	 * @return array Modified result set.
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
	 * Returns the enumerable options for specified field.
	 *
	 * @param object $Model Model object reference.
	 * @param string $field Enumerable field to query.
	 * @return array Enumerable fields, or an empty array if invalid field name.
	 */
	public function enumerate(&$Model, $field) {
		if (!isset($this->settings[$Model->name][$field])) {
			return array();
		}
		return $this->settings[$Model->name][$field];
	}

}
?>