<?php

// plugins/MauticExtendedFieldGroupBundle/EventListener/ConfigSubscriber.php

namespace MauticPlugin\MauticExtendedFieldGroupBundle\EventListener;

use Mautic\ConfigBundle\Event\ConfigEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\ConfigBundle\ConfigEvents;
use Mautic\ConfigBundle\Event\ConfigBuilderEvent;
use MauticPlugin\MauticExtendedFieldGroupBundle\Helper\MauticExtendedFieldGroupHelper;

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
    
    $translator = $this->factory->getTranslator();
    
    // Manipulate the values
    if (!empty($values['extendedfieldgroup_config']['extendedfieldgroup_config'])) {
      $gconf = $this->request->get('config');
      $_config = $gconf['extendedfieldgroup_config']['extendedfieldgroup_config']['list'];
      $values['extendedfieldgroup_config']['extendedfieldgroup_config']['list'] = array_values($_config);
      MauticExtendedFieldGroupHelper::initFactory($this->factory);
      /*make sure that assigned group is not deleted*/
      $validation = MauticExtendedFieldGroupHelper::preSaveValidate($values['extendedfieldgroup_config']['extendedfieldgroup_config']['list']);
      if(!$validation['success']){
        $event->setError($validation['message'], $validation['messageParams']);
        $event->stopPropagation();
      }
    }
    // Set updated values
    $event->setConfig($values);
  }

}
