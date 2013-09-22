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
<?php use_javascript("/opChargePlugin/js/bootstrap-button.js","last") ?>
<?php use_javascript("/opChargePlugin/js/bootstrap-modal.js","last") ?>
<?php use_javascript("/opChargePlugin/js/jquery.validate.js","last") ?>
<?php use_javascript("/opChargePlugin/js/messages_ja.js","last") ?>

<!--
■       ■   ■ ■ ■ ■ ■   ■       ■   ■       
■       ■       ■       ■ ■   ■ ■   ■       
■       ■       ■       ■   ■   ■   ■       
■ ■ ■ ■ ■       ■       ■   ■   ■   ■       
■       ■       ■       ■       ■   ■       
■       ■       ■       ■       ■   ■       
■       ■       ■       ■       ■   ■ ■ ■ ■ ■
-->
<div id="MailAddressLogin" class="loginForm">
  <h1>
    登録するメールアドレスを入力してください。
  </h1>
  <form id="mailform">
  <table>
    <tr>
      <td>
        <input type="email" class="span7" name="regist_email" placeholder="メールアドレス" id="regist_email" required/>
      </td>
    </tr>
    <tr>
      <td>
        <input type="submit" id="submit_mailregist" type="button" data-loading-text="通信中..." class="btn btn-primary btn-large pull-right" value="次へすすむ">
      </td>
    </tr>
  </table> 
  </form>
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
    <h3 id="myModalLabel">メールをご確認ください</h3>
  </div>
  <div class="modal-body">
    <p>入力したアドレス宛に、登録案内のメールを送信しました。メールの内容に従い、登録を続けてください。</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">閉じる</button>
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

jQuery.validator.setDefaults({
  debug: true,
  success: "valid"
});


$("#mailform").validate(
{
  rules: {
    regist_email: {
      required: true,
      email: true
    }
  }
}
);

function reset_default(){
  $("#submit_mailregist").button("reset");
  $("#regist_email").val("");
}

$(document).ready(function(){
  $("#mailform").submit(function (event) {
    event.preventDefault();
    if(!$(this).valid()){
      return;
    }

    $("#submit_mailregist").button("loading");

    $.ajax({
       type: "GET",
       dataType: 'json',
       url: "/api.php/r/mail.json",
       data: {"email": $("#regist_email").val()},
       success: function(msg){
        if("success" == msg.status){
          $('#myModal').modal('show');
        }else{
          alert(msg.message);
          reset_default();
        }
      }
    });
  });
});


$('#myModal').on('hidden', function () {
  reset_default();
})

</script>