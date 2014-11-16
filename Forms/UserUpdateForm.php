<?php
namespace Fercpe\Forms;

use Laracasts\Validation\FormValidator;

class UserUpdateForm extends FormValidator
{
	protected $rules = array(
		'Username'=>'required',
		'FirstName'=>'required',
		'LastName'=>'required',
	);
}//LoginForm