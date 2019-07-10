<form style="display: inline-block" method="post" action="/auth/login">
    <input name="login" placeholder="Enter login">
    <span style="color: red"><? if(!empty($loginErr)) {echo $loginErr;}
        elseif (!empty($emptyPassLogErr)) {echo $emptyPassLogErr;}?></span></br>
    <input name="pass" placeholder="Enter password">
    <span style="color: red"><? if(!empty($passErr)) {echo $passErr;}?></span></br>
    <button type="submit">Login</button>
</form>
<form style="display: inline-block; padding-left: 150px" method="post" action="/auth/register">
    <input name="login" placeholder="Enter login">
    <span style="color: red"><? if(!empty($nameErr)) {echo $nameErr;}
        elseif (!empty($emptyRegPassLogErr)) {echo $emptyRegPassLogErr;}?></span></br>
    <input name="pass" placeholder="Enter password"></br>
    <button type="submit">Sign up</button>
</form>