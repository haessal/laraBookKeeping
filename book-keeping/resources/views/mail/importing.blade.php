<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
    </head>
    <body>
        <p>
            Refer to the attached file for details of the results.
            <br />
        </p>
        <table>
            <tbody>
                <tr>
                    <td>sourceUrl:</td>
                    <td>{{ $sourceUrl }}</td>
                </tr>
                <tr>
                    <td>status:</td>
                    <td>{{ $status }}</td>
                </tr>
                @if (isset($errorMessage))
                <tr>
                    <td>error:</td>
                    <td>{{ $errorMessage }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        <br />
        <address>BookKeeping &copy 2007 haessal</address>
    </body>
</html>
