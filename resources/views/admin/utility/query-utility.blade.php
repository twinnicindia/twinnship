<html>
    <head>
        <title>Twinnship | Query Utility</title>
    </head>
    <body>
        <form method="post">
            @csrf
            <label>Enter Your Query</label><br>
            <textarea name="q" rows="5" placeholder="Enter Query" style="width:90%;">{{$query ?? ''}}</textarea><br>
            <input type="submit" value="Get Results"> &nbsp;
            <input type="submit" name="export" value="Export">
        </form>
        <table border="1" cellpadding="5px" cellspacing="5px">
            <thead>
                <tr>
                    @foreach($allHeaders as $h)
                    <td>{{strtoupper($h)}}</td>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($results as $r)
                <tr>
                    @foreach($allHeaders as $h)
                    <td>{{$r->$h}}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>
