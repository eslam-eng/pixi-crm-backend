<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Facebook Login Test</title>
</head>
<body>
  <h2>Facebook Login Test</h2>

  <!-- حالة تسجيل الدخول -->
  <div id="status"></div>

  <!-- Facebook Login Button -->
  <div id="fbLoginButton" style="display: none;">
    <fb:login-button 
      scope="email,public_profile"
      onlogin="checkLoginState();"
      size="large"
      button_type="continue_with"
      use_continue_as="false">
    </fb:login-button>
  </div>

  <!-- Custom Login Button (fallback) -->
  <button id="loginBtn" onclick="loginWithFacebook()" style="display: none;">Login with Facebook</button>

  <!-- زرار تسجيل الخروج -->
  <button id="logoutBtn" onclick="logoutFromFacebook()" style="display: none;">Logout from Facebook</button>

  <!-- مكان عرض النتيجة -->
  <pre id="result"></pre>

  <script>
    // تحميل SDK
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '1350583286399222', // ضع App ID تبعك
        cookie     : true,
        xfbml      : true,
        version    : 'v20.0' // نسخة الـ Graph API
      });

      // فحص حالة تسجيل الدخول عند تحميل الصفحة
      FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
      });
    };

    (function(d, s, id){
       var js, fjs = d.getElementsByTagName(s)[0];
       if (d.getElementById(id)) {return;}
       js = d.createElement(s); js.id = id;
       js.src = "https://connect.facebook.net/en_US/sdk.js";
       fjs.parentNode.insertBefore(js, fjs);
     }(document, 'script', 'facebook-jssdk'));

    // دالة معالجة تغيير حالة تسجيل الدخول
    function statusChangeCallback(response) {
      console.log('Status change callback:', response);
      
      const statusDiv = document.getElementById('status');
      const loginBtn = document.getElementById('loginBtn');
      const logoutBtn = document.getElementById('logoutBtn');
      
      if (response.status === 'connected') {
        // المستخدم مسجل الدخول في Facebook والتطبيق
        statusDiv.innerHTML = '<p style="color: green;">✅ Connected to Facebook</p>';
        loginBtn.style.display = 'none';
        logoutBtn.style.display = 'inline-block';
        
        // عرض معلومات المستخدم
        displayUserInfo(response.authResponse);
        
        // إرسال التوكن للخادم
        sendTokenToServer(response.authResponse);
        
      } else if (response.status === 'not_authorized') {
        // المستخدم مسجل الدخول في Facebook لكن ليس في التطبيق
        statusDiv.innerHTML = '<p style="color: orange;">⚠️ Logged into Facebook but not authorized for this app</p>';
        document.getElementById('fbLoginButton').style.display = 'block';
        loginBtn.style.display = 'none';
        logoutBtn.style.display = 'none';
        
      } else {
        // المستخدم غير مسجل الدخول في Facebook
        statusDiv.innerHTML = '<p style="color: red;">❌ Not logged into Facebook</p>';
        document.getElementById('fbLoginButton').style.display = 'block';
        loginBtn.style.display = 'none';
        logoutBtn.style.display = 'none';
      }
    }

    // دالة عرض معلومات المستخدم
    function displayUserInfo(authResponse) {
      FB.api('/me', {fields: 'id,name,email'}, function(userInfo) {
        console.log('User info:', userInfo);
        document.getElementById('result').textContent = 
          'Access Token: ' + authResponse.accessToken + '\n' +
          'User ID: ' + authResponse.userID + '\n' +
          'Expires In: ' + authResponse.expiresIn + ' seconds\n\n' +
          'User Info:\n' + JSON.stringify(userInfo, null, 2);
      });
    }

    // دالة إرسال التوكن للخادم
    function sendTokenToServer(authResponse) {
      fetch('/api/facebook/save-token', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          access_token: authResponse.accessToken,
          user_id: authResponse.userID,
          expires_in: authResponse.expiresIn
        })
      })
      .then(response => response.json())
      .then(data => {
        console.log('Token saved to server:', data);
        // Show success message
        document.getElementById('status').innerHTML = '<p style="color: green;">✅ Token saved successfully!</p>';
      })
      .catch(error => {
        console.error('Error saving token:', error);
        document.getElementById('status').innerHTML = '<p style="color: red;">❌ Error saving token: ' + error.message + '</p>';
      });
    }

    // دالة تسجيل الدخول
    function loginWithFacebook() {
      FB.login(function(response) {
        console.log('Login response:', response);
        statusChangeCallback(response);
      }, {scope: 'email,public_profile'}); // الصلاحيات المطلوبة
    }

    // دالة تسجيل الخروج
    function logoutFromFacebook() {
      FB.logout(function(response) {
        console.log('Logout response:', response);
        statusChangeCallback(response);
      });
    }

    // دالة فحص حالة تسجيل الدخول (لزر Facebook الرسمي)
    function checkLoginState() {
      FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
      });
    }
  </script>
</body>
</html>
