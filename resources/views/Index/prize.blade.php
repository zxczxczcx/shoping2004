<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>抽奖</title>
</head>
<body>
    <h1>抽奖中心</h1>
    <button id='prize_start'>点击抽奖</button>
</body>
</html>

<script type="text/javascript" src="index/js/jquery-3.5.1.js"></script>
<script>
    $(function(){
        $(document).on('click','#prize_start',function(){
            
            $.ajax({
                url:'/start',
                dataType:'json',
                success:function(res){
                    
                    if(res.error==400001){
                        alert(res.msg)
                        window.location.href='/login';

                        
                    }else if(res.error==0){
                        if(res.data.level==1){
                            alert('一等奖');
                        }else if(res.data.level==2){
                            alert('二等奖');
                        }else if(res.data.level==3){
                            alert('三等奖');
                        }else{
                            alert('谢谢惠顾');
                        }
                    }else if(res.error==400002){
                        alert(res.msg)
                    }
                }
            })
            
        })
    })
</script>