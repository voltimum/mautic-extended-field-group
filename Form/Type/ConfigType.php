<?php

/*
 * @copyright   2018 Voltimum S.A. All rights reserved
 * @author      Voltimum
 *
 * @link        http://voltimum.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedFieldGroupBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Mautic\CoreBundle\Form\Type\SortableListType;

/**
 * Class ConfigType.
 */
class ConfigType extends AbstractType {

  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $builder->add(
        'extendedfieldgroup_config', SortableListType::class, [
      'required' => true,
      'label' => 'mautic.extendedfieldgroup.config.label',
      'attr' => [
        'tooltip' => 'mautic.extendedfieldgroup.config.tooltip',
      ],
      'add_value_button' => 'mautic.extendedfieldgroup.config.btn.add',
      'option_required' => false,
      'with_labels' => false,
      'key_value_pairs' => false, // do not store under a `list` key and use label as the key
        ]
    );
    
  }

  /**
   * @return string
   */
  public function getName() {
    return 'extendedfieldgroup_config';
  }

}
