<?php 
namespace org\opencomb\oauth_friends_adapter ;

use org\jecat\framework\lang\aop\AOP;

use org\jecat\framework\ui\xhtml\weave\WeaveManager;

use org\opencomb\platform\ext\Extension ;

class OAuth_friends_adapter extends Extension 
{
	/**
	 * 载入扩展
	 */
	public function load()
	{
		$aWeaveMgr = WeaveManager::singleton() ;
		AOP::singleton()->register('org\\opencomb\\oauth_friends_adapter\\aspect\\RemoveFriendAspect') ;
		AOP::singleton()->register('org\\opencomb\\oauth_friends_adapter\\aspect\\CreateFriendAspect') ;
	}
}