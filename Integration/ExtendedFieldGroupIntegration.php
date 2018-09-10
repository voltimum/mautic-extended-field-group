<?php

/*
 * @copyright   2018 Voltimum S.A. All rights reserved
 * @author      Voltimum
 *
 * @link        http://voltimum.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedFieldGroupBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class ExtendedFieldGroupIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'ExtendedFieldGroup';
    }
    
    public function getIcon()
    {
        return 'plugins/MauticExtendedFieldGroupBundle/Assets/img/vlt-icon-extended-field-groups.png';
    }
    
    /**
     * Return's authentication method such as oauth2, oauth1a, key, etc.
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }
    
        /**
     * Return array of key => label elements that will be converted to inputs to
     * obtain from the user.
     *
     * @return array
     */
    public function getRequiredKeyFields()
    {
        return [
            //'group_name_prefix' => 'mautic.integration.voltimum.group_name_prefix',
        ];
    }


}
