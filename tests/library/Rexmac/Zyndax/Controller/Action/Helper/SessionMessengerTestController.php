<?php

namespace Rexmac\Zyndax\Controller\Action\Helper;

class SessionMessengerTestController extends \Zend_Controller_Action {
  public function indexAction() {
    $response = $this->getResponse();
    $sessionMessenger = $this->_helper->sessionMessenger;
    $response->appendBody(get_class($sessionMessenger));

    $messages = $sessionMessenger->getMessages();
    if(count($messages) === 0) $response->appendBody('1');

    $sessionMessenger->addMessage('foo');
    $messages = $sessionMessenger->getMessages();
    if(implode('', $messages) === 'foo') $response->appendBody('2');
  }
}

