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
        
        // On exécute le code js quand le document HTML est complétement chargé
        $(document).ready(function()
        {
            absgInit("<?php echo $selected_module; ?>");

            
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
    	<div id="header">
            <div class="slideshow">  
                <ul>
                <?php foreach ($slideshow as $p): ?>
                    <li><img src="<?php echo base_url() . 'assets/img/agpa/' . $p->year . '/mini/vignette_' . $p->filename; ?>" alt="" /></li>
                <?php endforeach; ?>
                </ul>  
            </div>
            <div class="overlay"><h1><span>A.G.P.A.<span></h1></div>
        </div>
        <div id="toolbar">
            <h1 class="mm-citation">Absolument G</h1>
            <ul id="mainMenu">
                <li class="mm-absg" onclick="javascript:go('');">&nbsp;<?php if ($user->notifications['absg'] > 0) : ?><span><?php echo $user->notifications['absg']; ?></span><?php endif; ?></li>
                <li class="mm-agpa" onclick="javascript:go('agpa');">&nbsp;<?php if ($user->notifications['agpa'] > 0) : ?><span><?php echo $user->notifications['agpa']; ?></span><?php endif; ?></li>
                <li class="mm-rules" onclick="javascript:go('agpa/rules');">&nbsp;</li>
                <li class="mm-archives" onclick="javascript:go('agpa/archives');">&nbsp;</li>
                <li class="mm-stats" onclick="javascript:go('agpa/stats');">&nbsp;</li>
                <li class="mm-palmares" onclick="javascript:go('agpa/palmares');">&nbsp;</li>
            </ul>
        </div>
        <?php if ($users_online['count'] > 0): ?>
        <ul id="online">
            <?php foreach ($users_online['users'] as $u): ?>
                <li>
                    <img class="<?php echo $u['class']; ?>" src="<?php echo $u['avatar']; ?>" alt="<?php echo $u['username']; ?>"/>
                    <div class="popup"><?php echo $u['username']; ?> : il y a <?php echo $u['delta']; ?><br/><?php echo $u['module']; ?></div>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <div id="content">
    	   <?php echo $output; ?>
        </div>
        <div id="footer">
            <?php echo $footerContent; ?>
            
            <p class="ellapsed">{elapsed_time}s</p>
        </div>
    </div>

    <script type="text/javascript">
    // <![CDATA[
        // Init variables
        $(function()
        {
            // On exécute l'animation de 40s du slide tout les 40secondes
            setInterval(function()
            {   
                var slideshow = $(".slideshow ul");
                var marge = slideshow.find("li:first").width();
                slideshow.animate({marginLeft:-marge},40000,"linear", function(){
                    $(this).css({marginLeft:0}).find("li:last").after($(this).find("li:first"));
                })
            }, 40000);

            // On lance l'animation du slide dès maintenant (et on la fait durer 1s de moins pour être sûr qu'elle ne se chevauchera pas avec le timer initialisé précédemment)
            var slideshow = $(".slideshow ul");
            var marge = slideshow.find("li:first").width();
            slideshow.animate({marginLeft:-marge},39999,"linear", function()
            {
                $(this).css({marginLeft:0}).find("li:last").after($(this).find("li:first"));
            });
        });
    // ]]>
    </script>
</body>
</html>