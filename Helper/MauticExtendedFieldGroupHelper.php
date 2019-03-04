<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedFieldGroupBundle\Helper;

use Mautic\CoreBundle\Factory\MauticFactory;
use Psr\Log\LoggerInterface;

class MauticExtendedFieldGroupHelper {

  /**
   * @var LoggerInterface
   */
  private static $logger;

  /**
   * @var IntegrationHelper
   */
  private static $factory;

  /**
   * @param MauticFactory $factory
   */
  public static function initFactory(MauticFactory $factory) {
    self::$factory = $factory;
  }

  /**
   * @param LoggerInterface $logger
   */
  public static function initLoggerInterface(LoggerInterface $logger) {
    self::$logger = $logger;
  }

  /**
   * @param        $msg
   * @param string $level
   */
  public static function log($msg, $level = 'error') {
    if (!self::$logger) {
      return;
    }
    try {
      self::$logger->log($level, $msg);
    } catch (\Exception $ex) {
      // do nothing
    }
  }

  public static function preSaveValidate(&$_config) {
    $translator = self::$factory->getTranslator();

    $assignedGroups = self::getAssignedCustomGroups();
    
    // Manipulate the values

    $machineNames = array();
    foreach ($_config as $i => $customGroup) {
      $machineNames[] = \Mautic\CoreBundle\Helper\InputHelper::filename($customGroup);
      $_config[$i] = htmlspecialchars($customGroup);
    }

    /* 1. check if all assigned custom groups are in teh list */
    foreach ($assignedGroups as $i => $groupName) {
      $_groupName = \Mautic\CoreBundle\Helper\InputHelper::filename($groupName);
      if (!in_array($_groupName, $machineNames)) {
        $key = 'mautic.lead.field.group.' . $_groupName;
        $groupNameTranslated = $translator->hasId($key) ? $translator->trans($key) : $_groupName;
        return [
          'success' => false,
          'message' => $translator->trans('mautic.extendedfieldgroup.config.error.group.missing'),
          'messageParams' => ['%groupName%' => $groupNameTranslated]
        ];
      }
    }

    /* 2. do not use standard group names */
    foreach ($machineNames as $i => $_groupName) {
      if (in_array($_groupName, $standardGroups)) {
        $key = 'mautic.lead.field.group.' . $_groupName;
        $groupNameTranslated = $translator->hasId($key) ? $translator->trans($key) : $_groupName;
        return [
          'success' => false,
          'message' => $translator->trans('mautic.extendedfieldgroup.config.error.group.is.standard'),
          'messageParams' => ['%groupName%' => $groupNameTranslated]
        ];
      }
    }

    return ['success' => true];
  }
  
  public static $ExtendedFieldsBundleGroups = [
      'core'         => 'mautic.lead.field.group.core', // Personally Identifiable
      'auto'         => 'mautic.lead.field.group.auto',
      'client'       => 'mautic.lead.field.group.client',
      'consent'      => 'mautic.lead.field.group.consent',
      'education'    => 'mautic.lead.field.group.education',
      'enhancement'  => 'mautic.lead.field.group.enhancement',
      'finance'      => 'mautic.lead.field.group.finance',
      'personal'     => 'mautic.lead.field.group.personal', // Health
      'home'         => 'mautic.lead.field.group.home',
      'politics'     => 'mautic.lead.field.group.politics',
      'professional' => 'mautic.lead.field.group.professional',
      'social'       => 'mautic.lead.field.group.social',
      'system'       => 'mautic.lead.field.group.system',
  ];

  /**

   * @return Array assigned custom groups
   */
  public static function getAssignedCustomGroups() {

    //$standardGroups = self::$factory->getModel('lead')->getRepository()->getFieldGroups();
    $standardGroups = array_keys(self::$ExtendedFieldsBundleGroups);
    
    /** @var \Mautic\LeadBundle\Model\FieldModel $fieldModel */
    $fieldModel = self::$factory->getModel('lead.field');
    $tableName = $fieldModel->getRepository()->getTableName();
    $tableAlias = $fieldModel->getRepository()->getTableAlias();
    $queryBuilder = $fieldModel->getRepository()->createQueryBuilder($tableAlias);
    $queryBuilder->select('distinct(' . $tableAlias . '.group) as field_group')
        ->where($tableAlias . '.group NOT IN (:standardGroups)')
        ->setParameter('standardGroups', $standardGroups, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
    $results = $queryBuilder->getQuery()->getArrayResult();
    $customGroups = array();
    foreach ($results as $i => $g) {
      foreach ($g as $key => $field_group) {
        $customGroups[$field_group] = $field_group;
      }
    }  
    return $customGroups;
  }

}
