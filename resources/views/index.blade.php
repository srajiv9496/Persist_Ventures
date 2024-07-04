<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Import CSV Data to MySQL database with Laravel</title>
    </head>

    <body>

        @if(Session::has('message'))
            <p >{{ Session::get('message') }}</p>
        @endif

        <h1>Import CSV Data to MySQL database with Laravel</h1>

        <form method='post' action='/uploadFile' enctype="multipart/form-data">
            @csrf
            <div>
                <input type="file" name="file"><br><br>
                <input type="email" name="email" placeholder="Your email"><br><br>
                <input type="submit" name="submit" value="Upload">
            </div>
        </form>

    </body>
</html>
