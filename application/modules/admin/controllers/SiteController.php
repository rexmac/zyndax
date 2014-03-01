<?php
/**
 * Zyndax
 *
 * LICENSE
 *
 * Zyndax is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zyndax is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zyndax.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License v3
 */

use \Exception,
    Rexmac\Zyndax\Log\Logger,
    Rexmac\Zyndax\Service\SiteContentService,
    Rexmac\Zyndax\View\Helper\Jquery as JqueryViewHelper,
    \Zend_Registry;

/**
 * Site management controller
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Controller
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_SiteController extends \Rexmac\Zyndax\Controller\Action {

  /**
   * Initialization
   *
   * @return void
   */
  public function init() {
    parent::init();
  }

  /**
   * Index action
   *
   * @return void
   */
  public function indexAction() {
  }

  /**
   * Preview action
   *
   * @return void
   */
  public function previewAction() {
    $request = $this->getRequest();
    Logger::debug(__METHOD__.':: referrer = '.$request->getServer('HTTP_REFERER', ''));
    Logger::debug(__METHOD__.':: siteDomain = '.Zend_Registry::get('siteDomain'));
    Logger::debug(__METHOD__.':: regex = ' . '/https?:\/\/' . preg_quote(Zend_Registry::get('siteDomain'), '/') . '\/site\/theme/');
    if(!preg_match('/https?:\/\/' . preg_quote(Zend_Registry::get('siteDomain'), '/') . '\/site\/theme/', $request->getServer('HTTP_REFERER', ''))) {
      Logger::crit(__METHOD__.':: Bad referrer');
      throw new Exception('');
    }

    Zend_Layout::getMvcInstance()->setLayoutPath(realpath(APPLICATION_PATH . '/layouts'));

    $this->view->assign(array(
    ));
    #$this->view->setScriptPath(APPLICATION_PATH . '/views/scripts/index');
    #$this->view->render('home.phtml');

    $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
    #$this->view->render('index/home.phtml');
    #$this->_helper->viewRenderer->setNoRender();
    $this->_helper->viewRenderer->renderScript('index/home.phtml');
  }

  /**
   * Privacy action
   *
   * @return void
   */
  public function privacyAction() {
    $request = $this->getRequest();
    $form = new \Admin_Form_SitePrivacyPolicy();

    if($request->isPost()) {
      if($form->isValid($request->getPost())) {
        $data = $form->getValues();

        try {
          #$data['privacyPolicy'];
          Logger::debug(__METHOD__.':: privacyPolicy ::'.$data['privacyPolicy']);
          SiteContentService::createOrUpdate(array(
            'name' => 'privacyPolicy',
            'content' => $data['privacyPolicy']
          ), array('name' => 'privacyPolicy'));

          $this->view->success = 1;
          $message = 'Privacy policy has been updated.';
          $this->_helper->sessionMessenger($message, 'success');
          return $this->_helper->redirector('index');
        } catch(Exception $e) {
          $this->getResponse()->setHttpResponseCode(500);
          $this->view->success = 0;
          $message = ('development' == APPLICATION_ENV ? $e->getMessage() : 'Application error: ASiPr001');
          $this->view->messages()->addMessage($message, 'error');
          Logger::err($e->getMessage());
        }
      } else { // Submitted form data is invalid
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->success = 0;
      }
    } else { // Not a POST request
    }

    $this->view->form = $form;
    $this->view->inlineScript()->appendFile('/js/libs/ckeditor/ckeditor.js');
    JqueryViewHelper::appendFile('/js/libs/ckeditor/adapters/jquery.js');
  }  // close privacyAction

  /**
   * Theme action
   *
   * @return void
   */
  public function themeAction() {
    $request = $this->getRequest();

    $form = new \Admin_Form_SiteTheme();

    if($request->isPost()) {
      if($form->isValid($request->getPost())) {
        $data = $form->getValues();
        try {
          // Uploaded image or remote image?
          if(isset($data['remoteImage']) && !empty($data['remoteImage'])) { // Remote image
          } else { // Uploaded image
            if(!$form->logo->isUploaded() || !$form->logo->receive()) {
              throw new Admin_SiteControllerException('Image upload failed');
            }
            Logger::debug(__METHOD__.':: logo = '.$form->logo->getFileName());
            $imgPath = $form->logo->getFileName();
          }

          $imageInfo = getimagesize($imgPath);
          $filename  = basename($imgPath);
          $image = array(
            'width'    => $imageInfo[0],
            'height'   => $imageInfo[1],
            'mime'     => $imageInfo['mime'],
            'filename' => $filename,
            'path'     => $imgPath
          );
          Logger::debug(__METHOD__.':: imageInfo = '.var_export($imageInfo, true));
          Logger::debug(__METHOD__.':: filename = '.$filename);

          // Move uploaded logo image to public accessible folder
          #rename($imgPath, APPLICATION_PATH . '/public/images/tmp/' . $filename);
          # or use something like /preview?type=image&name=xx script to return image from upload folder?
          # /preview?t=theme&n=filename.png

          $templateLogoMaxWidth = 286;
          $templateLogoMaxHeight = 77;
          Logger::debug(__METHOD__.":: maxWidth x maxHeight = $templateLogoMaxWidth x $templateLogoMaxHeight");

          if($imageInfo[0] > $templateLogoMaxWidth || $imageInfo[1] > $templateLogoMaxHeight) {
            /*
            // Use GD/ImageMagick to resize image to fit template
            $newWidth = $templateLogoMaxWidth;
            $newHeight = $templateLogoMaxHeight;
            if($imageInfo[0] > $imageInfo[1]) { // width > height
              $newHeight = $imageInfo[1] * ($templateLogoMaxHeight / $imageInfo[0]);
            } elseif($imageInfo[0] < $imageInfo[1]) { // width < height
              $newWidth = $imageInfo[0] * ($templateLogoMaxWidth / $imageInfo[1]);
            }
            Logger::debug(__METHOD__.":: newWidth x newHeight = $newWidth x $newHeight");

            $this->_resizeImage($image, $newWidth, $newHeight);
            */
            $this->_resizeImage($image, $templateLogoMaxWidth, $templateLogoMaxHeight);
            $imageInfo = getimagesize($image['path']);
            if($imageInfo[0] > $templateLogoMaxWidth || $imageInfo[1] > $templateLogoMaxHeight) {
              throw new Admin_SiteControllerException('Failed to resize image');
            }
          }

          $this->view->assign(array(
            'filename' => $filename,
            'width'    => $imageInfo[0],
            'height'   => $imageInfo[1],
            'success'  => 1
          ));
          #$message = 'Logo uploaded successfully.';
          #$this->_helper->sessionMessenger($message, 'success');
        } catch(Exception $e) {
          $this->getResponse()->setHttpResponseCode(500);
          $this->view->success = 0;
          $message = ('development' == APPLICATION_ENV ? $e->getMessage() : 'Application error: ASiT001');
          $this->view->messages()->addMessage($message, 'error');
          Logger::err($e->getMessage());
        }
      } else { // Subnmitted form data is invalid
        $this->getResponse()->setHttpResponseCode(500);
        $this->view->success = 0;
      }
    } else { // Not a POST request
    }

    $this->view->form = $form;

    // Special handling for AJAX form submissions
    if($request->isPost() && $request->isXmlHttpRequest()) {
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      $vars = $this->view->getVars();
      unset($vars['form']);
      #$this->getResponse()->setBody('<textarea>' . Zend_Json::encode($vars) . '</textarea>');
      $this->getResponse()
        ->setHeader('Content-Type', 'application/json')
        ->setBody(Zend_Json::encode($vars));
    }
  } // close themeAction

  /**
   * Resize image
   *
   * @param array $image Array of image info
   * @param int $width Desired image width
   * @param int $height Desired image height
   * @return void
   */
  private function _resizeImage(array $image, $width, $height) {
    $pathToConvert = '/usr/bin/convert';

    /*
    $quality = '';
    $sharpen = '';

    if($image['mime'] === 'image/jpeg') {
      $quality = '-quality 80';
      if((($width + $height) / ($image['width'] + $image['height'])) < 0.85) {
        $sharpen = '-sharpen 0x0.4';
      }
    } elseif($image['mime'] === 'image/png') {
      $quality = '-quality 95';
    } elseif($image['mime'] === 'image/gif') {
    }

    $cmd = escapeshellcmd($pathToConvert) .
      " {$quality} -background white -size {$image['width']} " .
      escapeshellarg($image['path']) .
      " -thumbnail " . escapeshellarg("{$width}x{$height}!") .
      " -depth 8 {$sharpen}" .
      escapeshellarg($image['path']) . ' 2>&1';
    */

    $cmd = escapeshellcmd($pathToConvert) . ' ' . escapeshellarg($image['path']) .
      ' -resize ' . escapeshellarg("{$width}x{$height}>") . ' ' . escapeshellarg($image['path']);

    Logger::debug(__METHOD__.':: cmd = '.$cmd);
    exec($cmd);
  }
}

/**
 * Admin site controller exception class
 *
 * @category   Zyndax
 * @package    Rexmac_Zyndax
 * @subpackage Application_Module_Admin_Controller_Exception
 * @copyright  Copyright (c) 2011-2012 Rexmac, LLC (http://rexmac.com/)
 * @author     Rex McConnell <rex@rexmac.com>
 */
class Admin_SiteControllerException extends \Zend_Exception {
}
