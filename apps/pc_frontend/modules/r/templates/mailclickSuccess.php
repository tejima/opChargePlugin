<!--
■ ■ ■ ■     ■       ■   ■ ■ ■ ■  
■       ■   ■       ■   ■       ■
■       ■   ■       ■   ■       ■
■ ■ ■ ■     ■ ■ ■ ■ ■   ■ ■ ■ ■  
■           ■       ■   ■        
■           ■       ■   ■        
■           ■       ■   ■        
-->
<?php use_javascript("jquery.min.js","first") ?>
<?php use_javascript("/opChargePlugin/js/bootstrap-modal.js","last") ?>


<!--
■       ■   ■ ■ ■ ■ ■   ■       ■   ■       
■       ■       ■       ■ ■   ■ ■   ■       
■       ■       ■       ■   ■   ■   ■       
■ ■ ■ ■ ■       ■       ■   ■   ■   ■       
■       ■       ■       ■       ■   ■       
■       ■       ■       ■       ■   ■       
■       ■       ■       ■       ■   ■ ■ ■ ■ ■
-->
<div id="MailAddressLogin" class="loginForm row">
  <div class="span7 offset1">
    <h1>クレジットカード情報を登録してください</h1>
    <form class="form-horizontal" id="my-form">
      <input type="hidden" name="token" value="<?php echo $token ?>">

      <div class="control-group">
        <label class="control-label" for="inputEmail">クレジットカード番号</label>
        <div class="controls">
          <input type="text" class="span4" placeholder="0000000000000000" name="number" />

        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputPassword">アルファベット</label>
        <div class="controls">
          <input type="text" class="span4" placeholder="TAROU YAMADA" name="name" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputPassword">有効期限(月/年)</label>
        <div class="controls">
          <select name="month" class="span2">
            <option>01</option>
            <option>02</option>
            <option>03</option>
            <option>04</option>
            <option>05</option>
            <option>06</option>
            <option>07</option>
            <option>08</option>
            <option>09</option>
            <option>10</option>
            <option>11</option>
            <option>12</option>
          </select>
          /
          <select name="year" class="span2">
            <option>2013</option>
            <option>2014</option>
            <option>2015</option>
            <option>2016</option>
            <option>2017</option>
            <option>2018</option>
            <option>2019</option>
            <option>2020</option>
            <option>2021</option>
            <option>2022</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="inputPassword">CVC</label>
        <div class="controls">
          <input type="text" class="span2" placeholder="000" name="cvc" />
          <a href="#myModal" data-toggle="modal">CVCとは？</a>
        </div>
      </div>

      <div class="control-group">
        <div class="controls">
          <label class="checkbox">
            <input type="checkbox" name="agree" value="true">利用規約に同意する</label>
        </div>
      </div>

      <div class="row">
        <div class="span6">
          <div class="control-group">
            <div class="controls">
              <button id="submit_creditcard" class="btn btn-primary btn-large btn-block">次へすすむ</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
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

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">CVCとは？</h3>
  </div>
  <div class="modal-body">
    <p>CVCとは、カードの裏面に◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯◯</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-large btn-block" data-dismiss="modal" aria-hidden="true">閉じる</button>
  </div>
</div>

<!-- Modal -->
<div id="doneModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="doneModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="doneModalLabel">登録完了</h3>
  </div>
  <div class="modal-body">
    <p>完了しました登録情報は、設定変更の際に必要になりますので必ずメモしてください。</p>
    <span id="my-message"></span>
  </div>
  <div class="modal-footer">
    <button class="btn btn-large btn-block" data-dismiss="modal" aria-hidden="true">登録情報をメモしました。閉じる</button>
  </div>
</div>

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
  $("#my-form").submit(function(event) {
    event.preventDefault();
    if(!$('#my-form [name=agree]:checked').val()){
      alert("利用規約OKしてね");
      return;
    }
    //name email month year cvc
    $.ajax({
        type: "GET",
        dataType: 'json',
        url: "/api.php/r/creditcard.json",
        data: $("#my-form").serialize(),
        success: function(msg){
          if("success" == msg.status){
            $('#my-message').val(msg.message);
            $('#doneModal').modal('show');
          }else{
            alert("サーバと通信できず、登録が完了しませんでした。しばらくしてから登録しなおしてください。");
          }
        },
        error: function(msg){
          alert("しばらくしてから登録しなおしてください。");
        }
    });
  });
});
$('#doneModal').on('hidden', function () {
   location.href = "/";
})
</script>

