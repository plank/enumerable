Enumerable Behavior
==================

ENUM column types are not supported by CakePHP for a variety of reasons.
However, there are occasions where you want exactly what an ENUM column type would provide.

The Enumerable behavior attempts to bridge this gap by providing some transparent
rewriting of fields marked as enumerable when returned in `Model::find()` calls.

A very typical use-case for Enumerable would be for a `gender` field in a user profile
model. Without Enumerable, you have two basic choices:

  1. use ENUMs, which are not supported in model schemas, but some workarounds
exists (e.g. custom Datasources).

  2. Define the column type as an `integer` or `char(1)`, and use class constants in the
model, e.g. `Profile::MALE` would map to `0` or `m`. This could be viable for some, but
requires some mental bookeeping.

Using Enumerable, you don't have to deal with either of these.

Setup
---

  - Create the columns in the table corresponding to the model that you wish to attach
    `Enumerable` to. The fields should be integer-ish (smallint for MySQL is best, but will
    work with any column type where you can store an integer value).
  - Add the `Enumerable.Enumerable` behavior to the model (see below for example configuration).

Example Configuration
---------------------

	/* app/models/profile.php */

    /**
     * Model behaviors
     *
     * @var array Behaviors, with optional configurations.
     */
    public $actsAs = array(
	    'Enumerable.Enumerable' => array(
		    'gender' => array(1 => 'male', 2 => 'female', 3 => 'minotaur', 4 => 'griffon')
	    )
    );

When calling a `Model::find()` method on a model configured to use `Enumerable`, the result
set data will be modified to substitute the integer values in the `gender` field for their
more human-readable counterparts.

Use
---

For the most part, the use of this behavior should be pretty much transparent - your use of
`Model::find()` and `Model::field()` on Enumerable-defined columns will work as expected.

If a mapping from an integer value to it's string representation does not exist, then the integer
value will be returned.

To obtain a `FormHelper::select()` compatible list of fields (to generate a 'gender' dropdown menu, 
for example), a convenience method is available: `EnumerableBehavior::enumerate('field_name')`. You
can use the results of this to send the data to your views in the typical manner, similar to performing
a `Model::find('list')` operation, but with no database query overhead:

	/* in a controller action */
    $this->set('gender', $this->ModelName->enumerate('field_name'));

If `field_name` is not marked as an Enumerable field, then `enumerate()` will return an empty array.

As always, the test cases included with the plugin demonstrate the proper usage of Enumerable, as well
as the possible functionality. If something does not work as intended, please open a ticket, or send a
pull request with a patch/fix. 

Thanks!