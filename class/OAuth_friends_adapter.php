<?php
namespace org\opencomb\oauth_friends_adapter;

use org\jecat\framework\lang\aop\AOP;
use org\opencomb\platform\ext\Extension;

class OAuth_friends_adapter extends Extension {
	/**
	 * 载入扩展
	 */
	public function load() {
		AOP::singleton ()->registerBean ( array (
				// jointpoint
				'org\\opencomb\\friends\\RemoveFriend::process()',
				// advice
				array (
						'org\\opencomb\\oauth_friends_adapter\\aspect\\RemoveFriendAspect',
						'process' 
				) 
		), __FILE__ )->
		registerBean ( array (
				// jointpoint
				'org\\opencomb\\friends\\CreateFriend::process()',
				// advice
				array (
						'org\\opencomb\\oauth_friends_adapter\\aspect\\CreateFriendAspect',
						'process'
				)
		),__FILE__) ;
	}
}