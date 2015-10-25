** Server requirements

- Linux Distribution (Tested on Ubuntu 12.04.4 LTS)
- PHP 5 >= 5.3.x
- Apache2 >= 2.2.x

** To run correctly this flow, localize the file config/config.inc.php

** And update your credentials in $CobrandCredentials: username / password and the $BaseURl with the EndPoint.

E.q:

	...
	public static $CobrandCredentials = array(
		"username" => "enter_your_username",
		"password" => "enter_your_password",
	);

	...
	public static $BaseURl = "[Enter the EndPoint]";
	...


** Save the change and run the application.

/> php -S localhost:3000 -t public/