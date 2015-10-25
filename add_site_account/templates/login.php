<style type="text/css">
  body {
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
  }

  .form-signin {
    max-width: 500px;
    padding: 19px 29px 29px;
    margin: 0 auto 20px;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    -webkit-border-radius: 5px;
       -moz-border-radius: 5px;
            border-radius: 5px;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
       -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
  }
  .form-signin .form-signin-heading,
  .form-signin .checkbox {
    margin-bottom: 10px;
  }
  .form-signin input[type="text"],
  .form-signin input[type="password"] {
    font-size: 16px;
    height: auto;
    margin-bottom: 15px;
    padding: 7px 9px;
  }

  .p-url-base {
  	padding:10px;
  	background: #E2E2E2;
  	border-radius: 10px;
  }
</style>
<div class="container">
  <form method="POST" id="form_login" class="form-signin">
    <h2 class="form-signin-heading">User Login</h2>
    <input type="text" class="input-block-level" value="" name="username" placeholder="Enter the username">
    <input type="password" class="input-block-level" value="" name="password" placeholder="Enter the password">
    <br>
    <input type="button" class="btn btn-large pull-right btn-primary btn-send-data" value="Login"/>
    <div style="clear:both;"></div>
    <div style="margin-top:100px;"> <img class="logo" src="<?= $baseAssets ?>/img/logo-yodleeinteractive-transparent.png"> 
    	<h4 class="description-app" style="display: inline;">  - API Test Drive</h4>
      <div class="pull-right" style="color: #B1AFAF;font-style: italic;">Flow: <?= $flow ?><span>
    </span></div>
    </div>
  </form>
</div> <!-- /container -->
<script type="text/javascript">
	$(function(){
		$(".btn-send-data").click(function(ev){
			ev.preventDefault();
      
			if($("input[name='username']").val()==""){
				alert("You must enter ther username.");
				return false;
			}

			if($("input[name='password']").val()==""){
				alert("You must enter the password");
				return false;
			}

			$.ajax({
				url: "<?= $baseURL ?>/check_login_flow",
				method:"POST",
				data: {
					"username":$("input[name='username']").val(),
					"password": $("input[name='password']").val(),
				},
        beforeSend:function(){
          $(".btn-send-data").attr("value","Loading...").attr("disabled", "disabled")
        },
				complete: function(xhr, status){
          var res = xhr.responseText;
          if(res=="true"){
            window.location.href="<?= (empty($baseURL)) ? '/' : $baseURL ?>";
          }else{
            alert(res);
          }
          $(".btn-send-data").val("Login").removeAttr("disabled");
				}
			})
		});
	});
</script>