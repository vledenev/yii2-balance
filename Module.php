<?php
namespace komer45\balance;

use Yii;
use yii\helpers\Html;
use yii\web\Session;
use komer45\balance\models\Score;
use komer45\balance\models\Transaction;

class Module extends \yii\base\Module
{
	public $userModel = null;
	public $adminRoles = ['admin'];
	public $otherRoles = ['user'];
	
	public function init()
    {
		parent::init();
		
		if (!isset($userModel)){
			$this->userModel = Yii::$app->user->identityClass;
		}
    }
	
	public function run()
    {

    }
	
}