<?php include 'init.php';?>
 <div class="row text-center">
    <?php 
        foreach (getItems(1) as $item) {
           ?>
           <div class="col-sm-6 col-md-4">
                <div class="thumbnail item-box">
                    <span class="price-tag"><?= $item['price']?></span>
                    <img calss="img-responsive" src="" alt="" >
                    <div class="caption">
                        <h3><?= $item['name']?></h3>
                        <p><?= $item['description']?></p>
                    </div>
                </div>
           </div>
           <?php
        }
    ?>
 </div>
 <?php include $tpl.'footer.php';?>