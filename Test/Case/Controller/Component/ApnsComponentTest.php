<?php

App::uses('Controller', 'Controller');
App::uses('ApnsComponent', 'Controller/Component');


/**
 * NotificationTestController class
 *
 * @package       com.ilbesculpi.Test.Case.Controller.Component
 */
class NotificationTestController extends Controller {

/**
 * name property
 *
 * @var string 'NotificationTest'
 */
	public $name = 'NotificationTest';

/**
 * uses property
 *
 * @var mixed null
 */
	public $uses = null;

/**
 * components property
 *
 * @var array
 */
	public $components = array(
		'Apns' => array (
			'passphrase' => 'somepassphrase',
			'cert' => 'cert.pem',
			'gateway' => 'gateway.sandbox.push.apple.com'
		)
	);

}


/**
 * ApnsTest class
 *
 * @package       com.ilbesculpi.Test.Case.Controller.Component
 */
class ApnsComponentTest extends CakeTestCase {
	
/**
 * Controller property
 *
 * @var NotificationTestController
 */
	public $Controller;

/**
 * name property
 *
 * @var string 'Apns'
 */
	public $name = 'Apns';
	
/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {

		$this->Controller = new NotificationTestController();

		$this->Controller->Components->init($this->Controller);

		$this->Controller->Apns->initialize($this->Controller);

		App::build(array(
			'View' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS)
		));
	}
	
/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		App::build();
		ClassRegistry::flush();
	}
	
/**
 * test Component configuration.
 *
 * @return void
 */
	public function testApnsConfiguration() {
		$this->assertEqual($this->Controller->Apns->settings['gateway'], 'gateway.sandbox.push.apple.com');
		$this->assertEqual($this->Controller->Apns->settings['cert'], 'cert.pem');
		$this->assertEqual($this->Controller->Apns->settings['passphrase'], 'somepassphrase');
	}
	
}