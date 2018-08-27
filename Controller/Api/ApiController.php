<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedFieldGroupBundle\Controller\Api;

use Mautic\ApiBundle\Controller\CommonApiController;
use FOS\RestBundle\Util\Codes;
use MauticPlugin\MauticExtendedFieldGroupBundle\Helper\MauticExtendedFieldGroupHelper;

/**
 * Class FocusApiController.
 */
class ApiController extends CommonApiController {

  public static $configName = 'extendedfieldgroup_config';

  /**
   * 
   * @return Response
   */
  public function listAction() {
    MauticExtendedFieldGroupHelper::initFactory($this->factory);
    $assignedGroups = MauticExtendedFieldGroupHelper::getAssignedCustomGroups();
    $config = $this->get('mautic.helper.core_parameters')->getParameter('mautic.' . self::$configName);
    $view = $this->view([self::$configName => $config, 'assigned_' . self::$configName => $assignedGroups], Codes::HTTP_OK);
    return $this->handleView($view);
  }

  public function updateAction() {

    $request = $this->container->get('request_stack')->getCurrentRequest();
    $list = $request->get(self::$configName);
    $list['list'] = array_keys(array_flip($list['list'])); 
    $configurator = $this->get('mautic.configurator');
    $paramethers = $configurator->getParameters();
    $isWritabale = $configurator->isFileWritable();
    $view = $this->view(['success' => true, 'list' => []], Codes::HTTP_OK);
    MauticExtendedFieldGroupHelper::initFactory($this->factory);
    $validation = MauticExtendedFieldGroupHelper::preSaveValidate($list['list']);
    if (!$validation['success']) {
      $view = $this->view($validation, Codes::HTTP_OK);
    } else {
      if ($isWritabale && md5(json_encode($list)) != md5(json_encode($paramethers[self::$configName]))) {
        try {
          $configurator->mergeParameters([self::$configName => $list]);
          $configurator->write();
          $cacheHelper = $this->get('mautic.helper.cache');
          $cacheHelper->clearContainerFile();
          $view = $this->view(['success' => true, 'list' => $list], Codes::HTTP_OK);
        } catch (\RuntimeException $exception) {
          $view = $this->view(['success' => false, 'message' => $exception->getMessage(), 'messageParams' => []], Codes::HTTP_OK);
        }
      }
    }
    return $this->handleView($view);
  }

}
