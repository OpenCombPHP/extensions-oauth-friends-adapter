<?php
namespace org\opencomb\oauth_friends_adapter\aspect;

use org\jecat\framework\mvc\controller\Request;

use org\opencomb\oauth_userstate_adapter\PushState;

use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\lang\aop\jointpoint\JointPointMethodDefine;

class CreateFriendAspect
{
	/**
	 * @pointcut
	 */
	public function pointcutCreateFriendAspect()
	{
		return array(
			new JointPointMethodDefine('org\\opencomb\\friends\\CreateFriend','process') ,
		) ;
	}
	
	/**
	 * @advice around
	 * @for pointcutCreateFriendAspect
	 */
	private function process()
	{
		
		// 调用原始原始函数
		aop_call_origin() ;
		
		if(\org\jecat\framework\auth\IdManager::singleton()->currentId())
		{
		    $aId = \org\jecat\framework\auth\IdManager::singleton()->currentId() ;
		    
		    $aModel = \org\jecat\framework\bean\BeanFactory::singleton()->createBean( $conf=array(
		            'class' => 'model' ,
		            'list'=>true,
		            'orm' => array(
		                    'table' => 'oauth:user' ,
		                    'keys'=>array('uid','service'),
		            ) ,
		    ), 'CreateFriendAspect' ) ;
		    
		    $oAuserClone = clone $aModel;
		    
		    $aModel->load($this->params['uid'],'uid') ;
		    
		    $oAuserClone->load($aId->userId(),'uid') ;

		    $sNoBindWeibo = array();
		    foreach($aModel->childIterator() as $o)
		    {
		        try{
		            $aAdapter = \org\opencomb\oauth\adapter\AdapterManager::singleton()->createApiAdapter($o->service) ;
		            foreach($oAuserClone->childIterator() as $o2){
		                if($o2->service == $o->service)
		                {
		                    $aRs = @$aAdapter->createFriendMulti($o2,$o->suid);
		                    $sNoBindWeibo[] = \org\opencomb\oauth\adapter\AdapterManager::singleton()->serviceTitle($o->service);
		                }
		            }
		            
		            
		        }catch(AuthAdapterException $e){
		            $this->createMessage(Message::error,$e->messageSentence(),$e->messageArgvs()) ;
		            $this->messageQueue()->display() ;
		            return ;
		        }
		    }
		    
		    $OAuthCommon = new \net\daichen\oauth\OAuthCommon("",  "");
		    $aRsT = $OAuthCommon -> multi_exec();
		    
		    $name = $aModel->child(0)->nickname?$aModel->child(0)->nickname:$aModel->child(0)->username;
		    if(!empty($sNoBindWeibo))
		    {
		        echo "您已经在 ".implode('、', $sNoBindWeibo)." 对 ".$name." 进行了同步关注。 ";
		    }
		    exit;
		    //echo "<pre>";print_r($aRsT);echo "</pre>";
		    //echo "<pre>";print_r($sNoBindWeibo);echo "</pre>";
		    //echo "<pre>";print_r(implode(",", $sNoBindWeibo));echo "</pre>";
		    //echo '<script>alert("'.implode(",", $sNoBindWeibo).'")</script>';
		}
	}
}
?>