<?php
function lang ($phras){
    static $lang=array(
        'ma'=>'mama'
    );
    return $lang[$phras];
}