<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="chrome=1, IE=edge">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>对比文件差异</title>

    <!-- Requires jQuery -->
    <script type="text/javascript" src="./js/jquery.min.js"></script>

    <!-- Requires CodeMirror -->
    <script type="text/javascript" src="./js/codemirror.min.js"></script>
    <script type="text/javascript" src="./js/searchcursor.min.js"></script>
    <link type="text/css" rel="stylesheet" href="./css/codemirror.min.css"/>

    <!-- Requires Mergely -->
    <script type="text/javascript" src="./js/mergely.js"></script>
    <link type="text/css" rel="stylesheet" href="./css/mergely.css"/>
</head>
<body>
<script type="text/javascript">
    $(document).ready(function () {
        $('#mergely').mergely({
            license: 'lgpl',
            cmsettings: {
                readOnly: true
            },
            lhs: function (setValue) {
                var lhs_url = '<?= $a; ?>';
                $.ajax({
                    type: 'GET', async: true, dataType: 'text',
                    url: lhs_url,
                    success: function (response) {
                        $('#path-lhs').text(lhs_url);
                        setValue(response);
                    }
                });
            },
            rhs: function (setValue) {
                var rhs_url = '<?= $b;?>';
                $.ajax({
                    type: 'GET', async: true, dataType: 'text',
                    url: rhs_url,
                    success: function (response) {
                        $('#path-rhs').text(rhs_url);
                        $('#compare').mergely('rhs', response);
                        setValue(response);
                    }
                });
            }
        });

    });
</script>
<div class="mergely-full-screen-8">
    <div class="mergely-resizer">
        <div id="mergely">
        </div>
    </div>
</div>
</body>
</html>