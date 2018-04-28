<!DOCTYPE html>
<html lang="fr">
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
    // ]]>
    </script>

</head>
<body>
    <div id="container" class="<?php echo "mm-" . $selected_module; ?>">
        <div id="content">
    	   <?php echo $output; ?>
        </div>
    </div>
</body>
</html>