<html>
    <head>
        <title> Custom Form Kit </title>
    </head>
    <body>
        <form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction">
            <input type=hidden name=encRequest value={{$encData}}>
            <input type=hidden name=access_code value={{$accessCode}}>
            <p>Redirecting Please Wait...</p>
        </form>
        <script language='javascript'>document.redirect.submit();</script>
    </body>
</html>
