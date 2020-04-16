<?php 

namespace Armincms;
 
use Armincms\NovaLogin\ReversedTwoFactorAuthenticator; 

class User extends ReversedTwoFactorAuthenticator 
{       
	public function domain() : ?string
	{ 
		return null;
	} 
}