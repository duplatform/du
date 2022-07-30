<!DOCTYPE html>
<html>

<head>
    <title>File not found.</title>
    <style>
        body {
            margin: 2rem;
            overflow: hidden;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        .t-box {
            display: ruby;
        }

        .t-box span,
        .t-box a {
            margin-bottom: 1rem;
            display: block;
        }
    </style>
</head>

<body>
    <h1>File Not Found.</h1>
    <div class="t-box">
        <span>Index</span> &nbsp;/&nbsp;
        <a class="a2-link" href="//127.0.0.1:8011/a2"> A2 Panel</a>
    </div>
    <table>
        <tr>
            <th>file</th>
            <th>size</th>
        </tr>
        <?php echo $str; ?>
    </table>
</body>

</html>
