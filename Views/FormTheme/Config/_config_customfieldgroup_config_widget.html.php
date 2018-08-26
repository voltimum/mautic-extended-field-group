<?php
// plugins/MauticExtendedFieldGroupBundle/Views/FormTheme/Config/_config_extendedfieldgroup_config_widget.html.php

?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('mautic.config.tab.extendedfieldgroup_config.head'); ?></h3>
    </div>
    <div class="panel-body">
        <?php foreach ($form->children as $f): ?>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($f); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>