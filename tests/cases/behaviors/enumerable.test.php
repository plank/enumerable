<?php

/**
 * Enumerable behavior test model
 *
 */
class Enumerable extends CakeTestModel {

	/**
	 * Behaviors
	 * @var array
	 * @access public
	 */
	public $actsAs = array(
		'Enumerable.Enumerable' => array(
			'gender' => array(1 => 'male', 2 => 'female', 3 => 'minotaur', 4 => 'griffon')
		)
	);

}

/**
 * Enumerable behavior test class
 *
 */
class EnumBehaviorTest extends CakeTestCase {

	/**
	 * Fixtures used in the behavior test
	 *
	 * @var array
	 * @access public
	 */
	public $fixtures = array('plugin.enumerable.enumerable');

	/**
	 * Start test case
	 *
	 * @return void
	 * @access public
	 */
	public function startCase() {
		$this->Enumerable = ClassRegistry::init('Enumerable');
		$this->EnumerableBehavior = new EnumerableBehavior();
	}

	public function endCase() {
		ClassRegistry::flush();
		unset($this->Enumerable, $this->EnumBehavior);
	}

	/**
	 * test behavior attachment to model
	 *
	 * @access public
	 */
	public function testBehaviorAttachment() {
		$this->assertEqual($this->Enumerable->Behaviors->attached(), array('Enumerable'));
	}

	/**
	 * Test settings parsing for behavior
	 *
	 * @return void
	 * @access public
	 */
	public function testSettings() {
		$settings = array('field_1' => array(1 => 'one', 2 => 'two', 3 => 'three'));

		$this->EnumerableBehavior->setup($this->Enumerable, $settings);
		$this->assertEqual($this->EnumerableBehavior->settings, array($this->Enumerable->alias => $settings));

		$settings = array();
		$this->expectError(new PatternExpectation('/You must define at least one Enumerable column/'));
		$this->EnumerableBehavior->setup($this->Enumerable, $settings);
	}

	/**
	 * Test behavior afterfind
	 *
	 * @return void
	 * @access public
	 */
	public function testAfterFind() {
		$alias = $this->Enumerable->alias;
		$data = array(
			array($alias => array('id' => 1, 'gender' => 1)),
			array($alias => array('id' => 2, 'gender' => 2)),
			array($alias => array('id' => 3, 'gender' => 1)),
		);

		$this->EnumerableBehavior->setup($this->Enumerable, array('gender' => array(1 => 'male', 2 => 'female')));
		$result = $this->EnumerableBehavior->afterFind($this->Enumerable, $data);

		$expected =  array(
			array($alias => array('id' => 1, 'gender' => 'male')),
			array($alias => array('id' => 2, 'gender' => 'female')),
			array($alias => array('id' => 3, 'gender' => 'male')),
		);

		$this->assertEqual($result, $expected);

	}

	/**
	 * Test behavior afterfind
	 *
	 * @return void
	 * @access public
	 */
	public function testAfterFindNoExistingMap() {
		$alias = $this->Enumerable->alias;
		$data = array(
			array($alias => array('id' => 1, 'gender' => 1)),
			array($alias => array('id' => 2, 'gender' => 2)),
			array($alias => array('id' => 3, 'gender' => 4)),
		);

		$this->EnumerableBehavior->setup($this->Enumerable, array('gender' => array(1 => 'male', 2 => 'female')));
		$result = $this->EnumerableBehavior->afterFind($this->Enumerable, $data);

		$expected =  array(
			array($alias => array('id' => 1, 'gender' => 'male')),
			array($alias => array('id' => 2, 'gender' => 'female')),
			array($alias => array('id' => 3, 'gender' => 4)),
		);

		$this->assertEqual($result, $expected);

	}

	public function testEnumerate() {
		$this->EnumerableBehavior->setup($this->Enumerable, array('gender' => array(1 => 'male', 2 => 'female', 3 => 'mermaid')));
		$result = $this->EnumerableBehavior->enumerate($this->Enumerable, 'gender');
		$expected = array(1 => 'male', 2 => 'female', 3 => 'mermaid');

		$this->assertEqual($result, $expected);

		$result = $this->EnumerableBehavior->enumerate($this->Enumerable, 'non_existant_column');
		$expected = array();

		$this->assertEqual($result, $expected);
	}

	/**
	 * Test model find integration
	 *
	 * @return void
	 * @access public
	 */
	public function testModelFind() {
		$name = $this->Enumerable->name;
		$results = $this->Enumerable->find('all');
		$this->assertEqual(count($results), 4);

		$data = array(
			array('id' => 1, 'gender' => 'male'),
			array('id' => 2, 'gender' => 'female'),
			array('id' => 3, 'gender' => 'minotaur'),
			array('id' => 4, 'gender' => 'female')
		);

		foreach ($results as $key => &$result) {
			$this->assertEqual(key($result), $name);
			$this->assertEqual($result[$name], $data[$key], "%s Find results do not match expected values.");
		}

		$this->Enumerable->id = 3;
		$result = $this->Enumerable->field('gender');
		$this->assertEqual($result, 'minotaur');

		$this->Enumerable->displayField = 'gender';
		$results = $this->Enumerable->find('list');

		$this->assertEqual(count($results), 4);
		$this->assertEqual($results[3], 'minotaur');

		$results = $this->Enumerable->find('all', array('fields' => 'id'));

		$data = array(
			array('id' => 1), array('id' => 2), array('id' => 3), array('id' => 4)
		);

		foreach ($results as $key => &$result) {
			$this->assertEqual(key($result), $name);
			$this->assertEqual($result[$name], $data[$key], "%s Find results do not match expected values.");
		}

	}

}
?>