<?php 

function fbnq($n){

    if($n<=1||$n<=2){
        return 1;
    }

    return fbnq($n-2)+fbnq($n-1);

}
echo fbnq(40);






?>