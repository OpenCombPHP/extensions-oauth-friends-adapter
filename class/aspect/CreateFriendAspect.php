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

		    
		    foreach($aModel->childIterator() as $o)
		    {
		        try{
		            $aAdapter = \org\opencomb\oauth\adapter\AdapterManager::singleton()->createApiAdapter($o->service) ;
		            foreach($oAuserClone->childIterator() as $o2){
		                if($o2->service == $o->service)
		                {
		                    $aRs = @$aAdapter->createFriendMulti($o2,$o->suid);
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
		    echo "<pre>";print_r($aRsT);echo "</pre>";
		}
	}
}
?>