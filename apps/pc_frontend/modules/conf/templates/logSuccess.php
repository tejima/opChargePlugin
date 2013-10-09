<!--
■ ■ ■ ■     ■       ■   ■ ■ ■ ■  
■       ■   ■       ■   ■       ■
■       ■   ■       ■   ■       ■
■ ■ ■ ■     ■ ■ ■ ■ ■   ■ ■ ■ ■  
■           ■       ■   ■        
■           ■       ■   ■        
■           ■       ■   ■        
-->

<!--
■       ■   ■ ■ ■ ■ ■   ■       ■   ■       
■       ■       ■       ■ ■   ■ ■   ■       
■       ■       ■       ■   ■   ■   ■       
■ ■ ■ ■ ■       ■       ■   ■   ■   ■       
■       ■       ■       ■       ■   ■       
■       ■       ■       ■       ■   ■       j
■       ■       ■       ■       ■   ■ ■ ■ ■ ■
-->
<div id="activity_list">
</div>

<!--
■ ■ ■ ■ ■   ■ ■ ■ ■ ■   ■       ■   ■ ■ ■ ■     ■               ■ ■ ■   ■ ■ ■ ■ ■   ■ ■ ■ ■ ■     ■ ■ ■ 
    ■       ■           ■ ■   ■ ■   ■       ■   ■             ■     ■       ■       ■           ■       ■
    ■       ■           ■   ■   ■   ■       ■   ■           ■       ■       ■       ■           ■       
    ■       ■ ■ ■ ■     ■   ■   ■   ■ ■ ■ ■     ■           ■       ■       ■       ■ ■ ■ ■       ■ ■ ■ 
    ■       ■           ■       ■   ■           ■           ■ ■ ■ ■ ■       ■       ■                   ■
    ■       ■           ■       ■   ■           ■           ■       ■       ■       ■           ■       ■
    ■       ■ ■ ■ ■ ■   ■       ■   ■           ■ ■ ■ ■ ■   ■       ■       ■       ■ ■ ■ ■ ■     ■ ■ ■ 
-->


<script id="activity_list_tmpl" type="text/x-jquery-tmpl">
  <div class="row">
    <div class="span3">
      ${body}
    </div>
  </div>
</script>

<!--
  ■ ■ ■       ■ ■ ■     ■ ■ ■ ■       ■ ■ ■     ■ ■ ■ ■     ■ ■ ■ ■ ■     ■ ■ ■ 
■       ■   ■       ■   ■       ■       ■       ■       ■       ■       ■       ■
■           ■           ■       ■       ■       ■       ■       ■       ■       
  ■ ■ ■     ■           ■ ■ ■ ■         ■       ■ ■ ■ ■         ■         ■ ■ ■ 
        ■   ■           ■   ■           ■       ■               ■               ■
■       ■   ■       ■   ■     ■         ■       ■               ■       ■       ■
  ■ ■ ■       ■ ■ ■     ■       ■     ■ ■ ■     ■               ■         ■ ■ ■ 
-->

<script>

$(document).ready(function(){
  $.ajax({
    type: "GET",
    dataType: 'json',
    url: "/api.php/activity/search.json",
    data: {"apiKey": openpne.apiKey},
    success: function(msg){
      if("success" == msg.status){
        $result = $('#activity_list_tmpl').tmpl(msg.data);
        $('#activity_list').html($result);
      }else{
        alert(msg.message);
        alert("サーバと通信できず、登録が完了しませんでした。しばらくしてから登録しなおしてください。");
      }
    },
    error: function(msg){
      //FIXME カード認証エラーは特別な対応を
      alert("しばらくしてから登録しなおしてください。");
    }
  });
});

</script>