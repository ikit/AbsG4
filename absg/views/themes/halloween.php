<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title; ?></title>
    <link rel="icon" href="<?=base_url()?>assets/img/favicon.png" type="image/png"/>
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
    <div id="container" class="<?php echo "mm-" . $selected_module; ?>">
    	<a name="top" id="header" href="<?php echo base_url(); ?>" title="Accueil du site">
        	<h1><span>Absolument G<span></h1>
        	<p><span><?php echo $citation['author']; ?> : </span> <?php echo $citation['citation']; ?></p>
        </a>
        <div id="toolbar">
            <h1 class="mm-citation"><a id="goToTopLink" href="#top"><span>top</span></a><a href="<?php echo base_url(); ?>" title="Accueil du site">Absolument G</a></h1>
            <div id="userMenu">
                <span><?php echo $user->username; ?></span>
                <img src="<?php echo $avatar_url; ?>" alt="<?php echo $user->username; ?>"/>
                <div id="userMenuPopup">
                    <ul id="mainMenu">
                        <li><a href="<?php echo $base_url; ?>user/profil" title="Mes informations">Mon profil</a></li>
                        <li><a href="<?php echo $base_url; ?>user/profilpwd" title="Mon mot de passe">Mon mot de passe</a></li>
                        <li><a href="<?php echo $base_url; ?>user/logout" title="Se déconnecter">Se déconnecter</a></li>
                    </ul>
                </div>
            </div>
            <ul id="mainMenu">
                <?php if ($user->rootfamilly == "gueudelot") : ?>
                <li class="mm-photo" onclick="javascript:go('photo');">&nbsp;
                    <div id="ms-photo" class="mm-subtitle">Photothèque (Gueudelot)</div></li>
                <?php endif; ?>
                <li class="mm-citation" onclick="javascript:go('citation');">&nbsp;
                    <?php if ($user->notifications['citations'] > 0) : ?><span><?php echo $user->notifications['citations']; ?></span><?php endif; ?>
                    <div id="ms-citation" class="mm-subtitle">Citations</div></li>
                <li class="mm-immt" onclick="javascript:go('immt');">&nbsp;
                    <?php if ($user->notifications['immt'] > 0) : ?><span><?php echo $user->notifications['immt']; ?></span><?php endif; ?>
                    <div id="ms-immt" class="mm-subtitle">Images du moment</div></li>
                <li class="mm-forum" onclick="javascript:go('forum');">&nbsp;
                    <?php if ($user->notifications['forum']['total'] > 0) : ?><span><?php echo $user->notifications['forum']['total']; ?></span><?php endif; ?>
                    <div id="ms-forum" class="mm-subtitle">Forum</div></li>
                <li class="mm-agpa" onclick="javascript:go('agpa');">&nbsp;
                    <div id="ms-agpa" class="mm-subtitle">A.G.P.A.</div></li>
                <li class="mm-agenda" onclick="javascript:go('agenda');">&nbsp; <!-- annuaire, calendrier, chronoloG, voyaG, Gnéalogie -->
                    <div id="ms-agenda" class="mm-subtitle">Agenda</div></li>
                <li class="mm-web3g" onclick="javascript:go('web3g');">&nbsp;
                    <?php if ($user->notifications['web3g'] > 0) : ?><span><?php echo $user->notifications['web3g']; ?></span><?php endif; ?>
                    <div id="ms-web3g" class="mm-subtitle">Web des G</div></li>
                <li class="mm-cultureg" onclick="javascript:go('cultureg');">&nbsp;
                    <div id="ms-cultureg" class="mm-subtitle">Culture G</div></li>
                <li class="mm-gtheque" onclick="javascript:go('gtheque');">&nbsp;
                    <div id="ms-gtheque" class="mm-subtitle">G-thèque</div></li>
                <li class="mm-wikig" onclick="javascript:go('wikig');">&nbsp;
                    <div id="ms-wikig" class="mm-subtitle">Wiki</div></li>
                <li class="mm-olympiages" onclick="javascript:go('olympiages');">&nbsp;
                    <div id="ms-olympiaGes" class="mm-subtitle">OlypiaGes</div></li>
                <li class="mm-grenier" onclick="javascript:go('grenier');">&nbsp;
                    <div id="ms-grenier" class="mm-subtitle">Grenier</div></li>
            </ul>
        </div>
        <!--<img id="zaffanerie" src="<?php echo $zaffanerie['url']; ?>" alt="<?php echo $zaffanerie['alt']; ?>"/>-->
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
            
            <p class="ellapsed">{memory_usage} - {elapsed_time}s</p>
        </div>
    </div>
</body>
</html>