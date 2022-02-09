<html lang="en">
<head>
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id"
          content="570301793131-5hbkkde70q5hvi2879deu6163f6bcjv0.apps.googleusercontent.com">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

</head>
<body>
<div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
<script>

    let clientId = $("meta[name=google-signin-client_id]").attr('content');

    function onSignIn(googleUser) {
        // Useful data for your client-side scripts:
        var profile = googleUser.getBasicProfile();
        console.log("ID: " + profile.getId()); // Don't send this directly to your server!
        console.log('Full Name: ' + profile.getName());
        console.log('Given Name: ' + profile.getGivenName());
        console.log('Family Name: ' + profile.getFamilyName());
        console.log("Image URL: " + profile.getImageUrl());
        console.log("Email: " + profile.getEmail());

        // The ID token you need to pass to your backend:
        var id_token = googleUser.getAuthResponse().id_token;
        console.log("ID Token: " + id_token);

        $.post("http://localhost:9292/api/v1/oauth/social", {
            client_id: clientId,
            token: id_token,
            provider: 'google',
            application: 'inventory',
            application_cli: 'zuragan_pos_mobile'
        }).done(function (data) {
            console.info(arguments);
        });


    };
</script>
</body>
</html>