<?php

/*
 * @author : owliber
 * @date : 2014-03-30
 */
?>
<h3>Purchasing</h3>

<?php $this->renderPartial('_search',array('model'=>$model)); ?>
<?php 
    if(is_array($member))
        $this->renderPartial('_main',array('model'=>$model,'dataProvider'=>$dataProvider,'member'=>$member,'totals'=>$totals)); 

?>
