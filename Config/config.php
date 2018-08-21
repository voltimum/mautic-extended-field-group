<?php

/*
 * @copyright   2018 Voltimum SA. All rights reserved
 * @author      voltimum-vk
 *
 * @link        http://voltimum.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
  'name' => 'Mautic Extended Field Group',
  'description' => 'Extend Custom Fields Group for Mautic framework',
  'version' => '1.0.0',
  'author' => 'Voltimum',
  'parameters' => [
    'extendedfieldgroup_config' => array(),
    'enable_extendedfieldgroup' => 1,
  ],
  'services' => [
    'integrations' => [
      'mautic.integration.extendedfieldsgroup' => [
        'class' => \MauticPlugin\MauticExtendedFieldGroupBundle\Integration\ExtendedFieldGroupIntegration::class,
        'arguments' => [
          'mautic.helper.core_parameters'
        ],
      ]
    ],
    'events' => [
      'mautic.extendedfieldgroup.config.subscriber' => [
        'class' => \MauticPlugin\MauticExtendedFieldGroupBundle\EventListener\ConfigSubscriber::class,
      ]
    ],
    'forms' => [
      'mautic.extendedfieldgroup_config.form.config' => [
        'class' => \MauticPlugin\MauticExtendedFieldGroupBundle\Form\Type\ConfigType::class,
        'alias' => 'extendedfieldgroup_config'
      ]
    ],
    'other' => [
      'mautic.form.extension.extended_field_group' => [
        'class' => \MauticPlugin\MauticExtendedFieldGroupBundle\Form\ExtendedFieldExtension::class,
        'arguments' => ['mautic.factory'],
        'tag' => 'form.type_extension',
        'tagArguments' => [
          'extended_type' => \Mautic\LeadBundle\Form\Type\FieldType::class,
        ],
      ],
    ]
  ]
];
