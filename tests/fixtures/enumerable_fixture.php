<?php
/**
 * Enumerable: Because ENUMS can suck, and humans like words.
 *
 * @copyright     Copyright 2010, Plank Design (http://plankdesign.com)
 * @license       http://opensource.org/licenses/mit-license.php The MIT License
 */

class EnumerableFixture extends CakeTestFixture {

	/**
	 * Table name
	 *
	 * @var string Table name
	 * @access public
	 */
	public $table = 'enumerables';

	public $fields = array(
		'id' => array('type' => 'integer', 'length' => 11, 'null' => false, 'key' => 'primary'),
		'gender' => array('type' => 'integer', 'length' => 2, 'null' => false),
		'indexes' => array('PRIMARY'=> array('column' => 'id', 'unique' => 1))
	);

	/**
	 * Record fixtures
	 *
	 * @var array
	 * @access public
	 */
	public $records = array(
		array('id' => 1, 'gender' => 1),
		array('id' => 2, 'gender' => 2),
		array('id' => 3, 'gender' => 3),
		array('id' => 4, 'gender' => 2)
	);

}
?>