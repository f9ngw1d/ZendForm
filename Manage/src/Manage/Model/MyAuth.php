<?php

namespace Manage\Model;

use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;

class MyAuth{
	protected $adapter;
	public function __construct(){
		$this->adapter = new DbAdapter(array(
			'driver' => 'Pdo_Mysql',
			//'database' => 'master',
			'database' => 'student_camp',
			'host' => 'localhost',
			'username'=>'root',
			'password'=>''
		));
	}
	//public function auth($email,$password){//进行认证
    //public function auth($uid,$password){//进行认证
	public function auth($identity,$credential,$tablename,$identityColumn){//进行认证
        $credential = md5($credential);
		$authAdapter = new AuthAdapter($this->adapter);
		$authAdapter
			->setTableName($tablename)//认证的数据表
            ->setIdentityColumn($identityColumn)//认证字段
//            ->setTableName("usr_student")//认证的数据表
//			->setIdentityColumn('examid')//认证字段
			->setCredentialColumn('password');//校验字段
		$authAdapter
			->setIdentity($identity)//认证值？
			->setCredential($credential);//校验值？
		$auth = new AuthenticationService();//实例化一个认证服务，以实现持久化认证
		$result = $auth->authenticate($authAdapter);

		if($result->isValid()){
			$auth->getStorage()->write($authAdapter->getResultRowObject());
			//echo "auth valid<br/>";
			return true;
		}
		//echo"auth invalid<br/>";
		return false;
	}

	public function isAuth(){//通过持久性判断认证是否一通过
		$auth = new AuthenticationService();
		if($auth->hasIdentity()) return true;
		return false;
	}
}
