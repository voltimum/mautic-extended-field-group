<?php

// plugins/MauticExtendedFieldGroupBundle/EventListener/ConfigSubscriber.php

namespace MauticPlugin\MauticExtendedFieldGroupBundle\EventListener;

use Mautic\ConfigBundle\Event\ConfigEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\ConfigBundle\ConfigEvents;
use Mautic\ConfigBundle\Event\ConfigBuilderEvent;

/**
 * Class ConfigSubscriber
 */
class ConfigSubscriber extends CommonSubscriber {

  /**
   * @return array
   */
  static public function getSubscribedEvents() {
    return array(
      ConfigEvents::CONFIG_ON_GENERATE => array('onConfigGenerate', 0),
      ConfigEvents::CONFIG_PRE_SAVE => array('onConfigSave', 0)
    );
  }

  /**
   * @param ConfigBuilderEvent $event
   */
  public function onConfigGenerate(ConfigBuilderEvent $event) {
    $event->addForm(
        array(
          'bundle' => 'MauticExtendedFieldGroupBundle',
          'formAlias' => 'extendedfieldgroup_config',
          'formTheme' => 'MauticExtendedFieldGroupBundle:FormTheme\Config',
          'parameters' => $event->getParametersFromConfig('MauticExtendedFieldGroupBundle')
        )
    );
  }

  /**
   * @param ConfigEvent $event
   */
  public function onConfigSave(ConfigEvent $event) {
    /** @var array $values */
    $values = $event->getConfig();
    
    $standardGroups = $this->factory->getModel('lead')->getRepository()->getFieldGroups();
    
    /*make sure that assigned group is not deleted*/
    $customGroups = $this->getAssignedCustomGroups();
    
    $translator = $this->factory->getTranslator();

    // Manipulate the values
    if (!empty($values['extendedfieldgroup_config']['extendedfieldgroup_config'])) {
      $_config = &$values['extendedfieldgroup_config']['extendedfieldgroup_config']['list'];
      $machineNames = array();
      foreach ($_config as $i => $customGroup) {
        $machineNames[] = \Mautic\CoreBundle\Helper\InputHelper::filename($customGroup);
        $_config[$i] = htmlspecialchars($customGroup);
      }
      
      /* 1. check if all assigned custom groups are in teh list */
      foreach ($customGroups as $i => $groupName) {
        $_groupName = \Mautic\CoreBundle\Helper\InputHelper::filename($groupName);
        if(!in_array($_groupName, $machineNames)){
          $key = 'mautic.lead.field.group.' . $_groupName;
          $groupNameTranslated = $translator->hasId($key) ? $translator->trans($key) : $_groupName;
          $event->setError($translator->trans('mautic.extendedfieldgroup.config.error.group.missing'), ['%groupName%' => $groupNameTranslated] );
          $event->stopPropagation();
        }
      }
      
      /* 2. do not use standard group names */
      foreach ($machineNames as $i => $_groupName) {
        if(in_array($_groupName, $standardGroups)){
          $key = 'mautic.lead.field.group.' . $_groupName;
          $groupNameTranslated = $translator->hasId($key) ? $translator->trans($key) : $_groupName;
          $event->setError($translator->trans('mautic.extendedfieldgroup.config.error.group.is.standard'), ['%groupName%' => $groupNameTranslated] );
          $event->stopPropagation();
        }
      }
      
    }

    // Set updated values 
    $event->setConfig($values);
  }

  /**
   * @return Array assigned custom groups
   */
  private function getAssignedCustomGroups() {

    $standardGroups = $this->factory->getModel('lead')->getRepository()->getFieldGroups();
    /** @var \Mautic\LeadBundle\Model\FieldModel $fieldModel */
    $fieldModel = $this->factory->getModel('lead.field');
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
