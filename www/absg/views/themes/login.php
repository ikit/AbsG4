<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>" />

    <!-- CSS -->
    <?php foreach($css as $url): ?>
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $url; ?>" />
    <?php endforeach; ?>

    <!-- JavaScripts -->
    <?php foreach($js as $url): ?>
        <script type="text/javascript" src="<?php echo $url; ?>"></script>
    <?php endforeach; ?>

    <script type="text/javascript">
    // <![CDATA[
        var baseURL = "<?php echo $base_url; ?>";
        //var documentBody = (($.browser.chrome)||($.browser.safari)) ? document.body : document.documentElement;
        
        // On exécute le code js quand le document HTML est complétement chargé
        $(document).ready(function()
        {
            absgInit("");
        });

        // Google Analytocs
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-76213819-1', 'auto');
        ga('send', 'pageview');
    // ]]>
    </script>
</head>
<body>
    <div id="container">
    	   <?php echo $output; ?>
    </div>
</body>
</html>