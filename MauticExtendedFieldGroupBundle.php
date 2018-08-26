<?php

/**
 * Description of MauticExtendedFieldGroupBundle
 * Improve an overview of extended fields - group extended fields
 *
 * @author vkocherga
 */

namespace MauticPlugin\MauticExtendedFieldGroupBundle;

use Mautic\PluginBundle\Bundle\PluginBundleBase;

class MauticExtendedFieldGroupBundle extends PluginBundleBase {

  public function boot() {

    $factory = $this->container->get('mautic.factory');
    /* @var CoreParametersHelper coreParameters */
    $coreParameters = $factory->getDispatcher()->getContainer()->get('mautic.helper.core_parameters');
    $config = $coreParameters->getParameter('extendedfieldgroup_config', array(array()));
    if (!empty($config['list'])) {
      $translator = $factory->getTranslator();
      foreach ($config['list'] as $i => $groupName) {
        $key = 'mautic.lead.field.group.' . \Mautic\CoreBundle\Helper\InputHelper::filename($groupName);
        $translator->getCatalogue()->set($key, $groupName);
      }
    }
  }
  
  

}
