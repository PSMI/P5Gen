<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="row">
    <div class="span3">
        <div id="sidebar">
        <?php 
            /* Get user access rights by account type */
            if(!Yii::app()->user->isGuest) UserMenu::userMenus(Yii::app()->session['account_type_id']);
            
            $this->beginWidget('zii.widgets.CPortlet');
            $this->widget('bootstrap.widgets.TbMenu', array(
                'type'=>'tabs',
                'stacked'=>true,
                'items'=>$this->menu,
                //'htmlOptions'=>array('class'=>'operations'),
            ));
            $this->endWidget();
        ?>
        </div><!-- sidebar -->
    </div>
    <div class="span9">
        <div id="content">
            <?php echo $content; ?>
        </div><!-- content -->
    </div>
    
</div>
<?php $this->endContent(); ?>