<?php

/*
 * @copyright   2018 Voltimum S.A. All rights reserved
 * @author      Voltimum
 *
 * @link        http://voltimum.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedFieldGroupBundle\Form;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\LeadBundle\Form\Type\FieldType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ExtendedFieldExtension.
 *
 * Updates the Mautic Lead Bundle FieldType.php for Object group choice values.
 */
class ExtendedFieldExtension extends AbstractTypeExtension
{
    /** @var CoreParametersHelper */
    protected $coreParameters;

    public function __construct(MauticFactory $factory)
    {
        /* @var CoreParametersHelper coreParameters */
        $this->coreParameters = $factory->getDispatcher()->getContainer()->get('mautic.helper.core_parameters');
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        // use FormType::class to modify (nearly) every field in the system
        return FieldType::class;
    }

    /**
     * Add a extended 'object' type to write to a corresponding table for that new extended value.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $disabled = !empty($options['data']) ? $options['data']->isFixed() : false;
        $choices = [
                    'core'         => 'mautic.lead.field.group.core',
                    'social'       => 'mautic.lead.field.group.social',
                    'personal'     => 'mautic.lead.field.group.personal',
                    'professional' => 'mautic.lead.field.group.professional',
                ];
        $config = $this->coreParameters->getParameter('extendedfieldgroup_config', array(array()));
        
        if(!empty($config['list'])){
          foreach ($config['list'] as $i => $groupName) {
            $key = \Mautic\CoreBundle\Helper\InputHelper::filename($groupName);
            $choices[$key] = 'mautic.lead.field.group.' . $key;
          }
        }
        
        $builder->add(
            'group',
            'choice',
            [
                'choices' => $choices,
                'attr' => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.lead.field.form.group.help',
                ],
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.lead.field.group',
                'empty_value' => false,
                'required'    => false,
                'disabled'    => $disabled,
            ]
        );
        
    }
}
